/**
 * Socket Test Utility
 * Dùng để test và debug socket connection
 */
class SocketTest {
    constructor() {
        this.testResults = [];
        this.isRunning = false;
    }

    runAllTests() {
        if (this.isRunning) {
            console.log('Tests đang chạy...');
            return;
        }

        this.isRunning = true;
        this.testResults = [];
        console.log('🚀 Bắt đầu test Socket Manager...');

        this.testSocketManagerExists()
            .then(() => this.testSocketConnection())
            .then(() => this.testStateManagement())
            .then(() => this.testReconnection())
            .then(() => this.showResults())
            .catch(error => {
                console.error('❌ Test failed:', error);
                this.isRunning = false;
            });
    }

    testSocketManagerExists() {
        return new Promise((resolve) => {
            console.log('📋 Test 1: Kiểm tra SocketManager tồn tại...');

            if (window.socketManager) {
                this.testResults.push({ test: 'SocketManager exists', status: 'PASS', message: 'SocketManager đã được khởi tạo' });
                console.log('✅ SocketManager tồn tại');
            } else {
                this.testResults.push({ test: 'SocketManager exists', status: 'FAIL', message: 'SocketManager chưa được khởi tạo' });
                console.log('❌ SocketManager không tồn tại');
            }

            resolve();
        });
    }

    testSocketConnection() {
        return new Promise((resolve) => {
            console.log('📋 Test 2: Kiểm tra kết nối socket...');

            if (window.socketManager && window.socketManager.isSocketAvailable()) {
                const status = window.socketManager.getConnectionStatus();
                this.testResults.push({
                    test: 'Socket connection',
                    status: 'PASS',
                    message: `Connected: ${status.connected}, Listeners: ${status.listenersCount}`
                });
                console.log('✅ Socket đã kết nối');
            } else {
                this.testResults.push({
                    test: 'Socket connection',
                    status: 'FAIL',
                    message: 'Socket chưa kết nối hoặc không khả dụng'
                });
                console.log('❌ Socket chưa kết nối');
            }

            resolve();
        });
    }

    testStateManagement() {
        return new Promise((resolve) => {
            console.log('📋 Test 3: Kiểm tra state management...');

            if (window.socketManager) {
                // Test thêm một registration vào state
                const testRegistration = {
                    id: 'test-123',
                    queue_number: '999',
                    full_name: 'Test User',
                    department_id: 1,
                    new_status: 'pending',
                    at: new Date().toISOString()
                };

                window.socketManager.updateRegistration(testRegistration);
                const retrieved = window.socketManager.getRegistration('test-123');

                if (retrieved && retrieved.id === 'test-123') {
                    this.testResults.push({
                        test: 'State management',
                        status: 'PASS',
                        message: 'State management hoạt động bình thường'
                    });
                    console.log('✅ State management hoạt động');
                } else {
                    this.testResults.push({
                        test: 'State management',
                        status: 'FAIL',
                        message: 'Không thể lưu/khôi phục state'
                    });
                    console.log('❌ State management không hoạt động');
                }

                // Cleanup
                window.socketManager.removeRegistration('test-123');
            } else {
                this.testResults.push({
                    test: 'State management',
                    status: 'FAIL',
                    message: 'SocketManager không tồn tại'
                });
            }

            resolve();
        });
    }

    testReconnection() {
        return new Promise((resolve) => {
            console.log('📋 Test 4: Kiểm tra reconnection logic...');

            if (window.socketManager) {
                // Simulate disconnect
                const originalConnected = window.socketManager.isConnected;
                window.socketManager.isConnected = false;

                // Test reconnection
                setTimeout(() => {
                    window.socketManager.isConnected = originalConnected;

                    if (window.socketManager.attemptReconnect) {
                        this.testResults.push({
                            test: 'Reconnection logic',
                            status: 'PASS',
                            message: 'Reconnection logic có sẵn'
                        });
                        console.log('✅ Reconnection logic có sẵn');
                    } else {
                        this.testResults.push({
                            test: 'Reconnection logic',
                            status: 'FAIL',
                            message: 'Reconnection logic không có sẵn'
                        });
                        console.log('❌ Reconnection logic không có sẵn');
                    }

                    resolve();
                }, 100);
            } else {
                this.testResults.push({
                    test: 'Reconnection logic',
                    status: 'FAIL',
                    message: 'SocketManager không tồn tại'
                });
                resolve();
            }
        });
    }

    showResults() {
        console.log('\n📊 KẾT QUẢ TEST:');
        console.log('================');

        let passCount = 0;
        let failCount = 0;

        this.testResults.forEach(result => {
            const icon = result.status === 'PASS' ? '✅' : '❌';
            console.log(`${icon} ${result.test}: ${result.message}`);

            if (result.status === 'PASS') passCount++;
            else failCount++;
        });

        console.log('================');
        console.log(`📈 Tổng kết: ${passCount} PASS, ${failCount} FAIL`);

        if (failCount === 0) {
            console.log('🎉 Tất cả tests đều PASS! Socket Manager hoạt động tốt.');
        } else {
            console.log('⚠️  Có một số tests FAIL. Vui lòng kiểm tra lại.');
        }

        this.isRunning = false;
    }

    // Utility methods
    simulateRegistrationCreated() {
        if (window.socketManager) {
            const testEvent = {
                id: Date.now(),
                queue_number: '001',
                full_name: 'Test User ' + Date.now(),
                department_id: 1,
                new_status: 'pending',
                at: new Date().toISOString()
            };

            console.log('🧪 Simulating registration.created event:', testEvent);

            // Trigger event manually
            const listeners = window.socketManager.listeners.get('registration-created');
            if (listeners && listeners.callback) {
                listeners.callback(testEvent);
            }
        }
    }

    simulateStatusUpdate() {
        if (window.socketManager) {
            const testEvent = {
                id: Date.now(),
                department_id: 1,
                new_status: 'processing'
            };

            console.log('🧪 Simulating status.updated event:', testEvent);

            // Trigger event manually
            const listeners = window.socketManager.listeners.get('status-updated');
            if (listeners && listeners.callback) {
                listeners.callback(testEvent);
            }
        }
    }

    getDebugInfo() {
        if (window.socketManager) {
            return {
                exists: !!window.socketManager,
                connected: window.socketManager.isSocketAvailable(),
                status: window.socketManager.getConnectionStatus(),
                state: {
                    registrationsCount: window.socketManager.getAllRegistrations().length,
                    lastUpdate: window.socketManager.state.lastUpdate
                }
            };
        }
        return { exists: false };
    }
}

// Auto-create test instance
window.socketTest = new SocketTest();

// Add to global for easy access
window.testSocket = () => window.socketTest.runAllTests();
window.simulateRegistration = () => window.socketTest.simulateRegistrationCreated();
window.simulateStatusUpdate = () => window.socketTest.simulateStatusUpdate();
window.getSocketDebugInfo = () => window.socketTest.getDebugInfo();

console.log('🔧 Socket Test Utility loaded. Use testSocket() to run tests.');
