/**
 * Global Socket Manager
 * Quản lý kết nối socket và state cho toàn bộ ứng dụng
 */
class SocketManager {
    constructor() {
        this.echo = null;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000;
        this.listeners = new Map();
        this.state = {
            registrations: new Map(),
            lastUpdate: null
        };

        this.init();
    }

    init() {
        // Kiểm tra xem có socket.io và Laravel Echo không
        if (typeof io === 'undefined' || typeof Echo === 'undefined') {
            console.warn('Socket.IO hoặc Laravel Echo chưa được load');
            return;
        }

        this.connect();
        this.setupPageVisibilityHandling();
        this.setupBeforeUnloadHandling();
    }

    connect() {
        try {
            // Tạo Echo instance mới
            const EchoCtor = (window.Echo && window.Echo.default) || window.Echo || window.LaravelEcho;

            this.echo = new EchoCtor({
                broadcaster: 'socket.io',
                host: `${location.hostname}:6001`,
                transports: ['websocket', 'polling'],
                forceNew: true, // Force tạo connection mới
                autoConnect: true
            });

            // Lắng nghe events
            this.echo.connector.socket.on('connect', () => {
                console.log('Socket connected');
                this.isConnected = true;
                this.reconnectAttempts = 0;
                this.reconnectAllListeners();
            });

            this.echo.connector.socket.on('disconnect', () => {
                console.log('Socket disconnected');
                this.isConnected = false;
                this.attemptReconnect();
            });

            this.echo.connector.socket.on('connect_error', (error) => {
                console.error('Socket connection error:', error);
                this.isConnected = false;
                this.attemptReconnect();
            });

            // Lưu vào global để các trang khác có thể sử dụng
            window.socketManager = this;

        } catch (error) {
            console.error('Error initializing socket:', error);
            this.attemptReconnect();
        }
    }

    attemptReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.error('Max reconnection attempts reached');
            return;
        }

        this.reconnectAttempts++;
        const delay = this.reconnectDelay * Math.pow(2, this.reconnectAttempts - 1); // Exponential backoff

        console.log(`Attempting to reconnect in ${delay}ms (attempt ${this.reconnectAttempts}/${this.maxReconnectAttempts})`);

        setTimeout(() => {
            if (!this.isConnected) {
                this.connect();
            }
        }, delay);
    }

    reconnectAllListeners() {
        // Reconnect tất cả listeners đã đăng ký
        this.listeners.forEach((listener, key) => {
            const { channel, event, callback } = listener;
            this.listen(channel, event, callback, key);
        });
    }

    listen(channel, event, callback, key = null) {
        if (!this.echo) {
            console.warn('Echo not initialized');
            return;
        }

        const listenerKey = key || `${channel}-${event}`;

        // Lưu listener để có thể reconnect sau
        this.listeners.set(listenerKey, { channel, event, callback });

        try {
            this.echo.channel(channel).listen(event, callback);
            console.log(`Listening to ${channel}.${event}`);
        } catch (error) {
            console.error(`Error listening to ${channel}.${event}:`, error);
        }
    }

    stopListening(channel, event, key = null) {
        const listenerKey = key || `${channel}-${event}`;
        this.listeners.delete(listenerKey);

        if (this.echo) {
            try {
                this.echo.channel(channel).stopListening(event);
            } catch (error) {
                console.error(`Error stopping listener ${channel}.${event}:`, error);
            }
        }
    }

    setupPageVisibilityHandling() {
        // Reconnect khi user quay lại tab
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && !this.isConnected) {
                console.log('Page visible, attempting to reconnect socket');
                this.connect();
            }
        });
    }

    setupBeforeUnloadHandling() {
        // Lưu state trước khi rời trang
        window.addEventListener('beforeunload', () => {
            this.saveState();
        });

        // Khôi phục state khi load trang
        window.addEventListener('load', () => {
            this.restoreState();
        });
    }

    saveState() {
        try {
            const stateData = {
                registrations: Array.from(this.state.registrations.entries()),
                lastUpdate: this.state.lastUpdate,
                timestamp: Date.now()
            };
            localStorage.setItem('socketManagerState', JSON.stringify(stateData));
        } catch (error) {
            console.error('Error saving socket state:', error);
        }
    }

    restoreState() {
        try {
            const savedState = localStorage.getItem('socketManagerState');
            if (savedState) {
                const stateData = JSON.parse(savedState);
                // Chỉ restore nếu data không quá cũ (5 phút)
                if (Date.now() - stateData.timestamp < 5 * 60 * 1000) {
                    this.state.registrations = new Map(stateData.registrations);
                    this.state.lastUpdate = stateData.lastUpdate;
                    console.log('Socket state restored');
                }
            }
        } catch (error) {
            console.error('Error restoring socket state:', error);
        }
    }

    updateRegistration(registration) {
        this.state.registrations.set(registration.id, {
            ...registration,
            updatedAt: Date.now()
        });
        this.state.lastUpdate = Date.now();
    }

    removeRegistration(id) {
        this.state.registrations.delete(id);
        this.state.lastUpdate = Date.now();
    }

    getRegistration(id) {
        return this.state.registrations.get(id);
    }

    getAllRegistrations() {
        return Array.from(this.state.registrations.values());
    }

    disconnect() {
        if (this.echo) {
            this.echo.disconnect();
            this.echo = null;
        }
        this.isConnected = false;
        this.listeners.clear();
    }

    // Utility methods
    isSocketAvailable() {
        return this.echo && this.isConnected;
    }

    getConnectionStatus() {
        return {
            connected: this.isConnected,
            reconnectAttempts: this.reconnectAttempts,
            listenersCount: this.listeners.size
        };
    }
}

// Auto-initialize khi DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (!window.socketManager) {
        window.socketManager = new SocketManager();
    }
});

// Export cho module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SocketManager;
}
