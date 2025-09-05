# Socket Reconnection Fix - Hệ thống bốc số thứ tự

## Vấn đề đã được giải quyết

**Vấn đề gốc:** Khi user đăng ký mới, record được gửi vào trang quản lý và cập nhật real-time, nhưng khi xem chi tiết rồi quay lại trang index thì mất các cập nhật từ socket và phải reload page mới hiện.

## Giải pháp đã triển khai

### 1. SocketManager Global (`public/js/socket-manager.js`)

Tạo một class quản lý socket connection toàn cục với các tính năng:

- **Auto-reconnection:** Tự động kết nối lại khi mất kết nối
- **State Management:** Lưu trữ state của registrations trong localStorage
- **Page Visibility Handling:** Reconnect khi user quay lại tab
- **Listener Management:** Quản lý và reconnect tất cả listeners
- **Exponential Backoff:** Tăng dần thời gian chờ khi reconnect thất bại

### 2. Cập nhật trang Index (`resources/views/backend/service-registrations/index.blade.php`)

- Sử dụng SocketManager thay vì tạo Echo instance riêng
- Tự động khôi phục state khi quay lại trang
- Reattach event listeners sau khi reconnect
- Lưu trữ registrations vào state để không bị mất khi navigate

### 3. Layout Global (`resources/views/layouts/app.blade.php`)

- Load SocketManager globally cho tất cả trang
- Đảm bảo socket connection được duy trì qua các trang

### 4. Test Utility (`public/js/socket-test.js`)

- Công cụ test và debug socket connection
- Simulate events để test functionality
- Hiển thị debug information

## Cách sử dụng

### 1. Kiểm tra Socket Manager

```javascript
// Trong browser console
window.socketManager.getConnectionStatus()
```

### 2. Test Socket Connection

Truy cập: `http://your-domain.com/socket-test`

- Click "Run All Tests" để kiểm tra tất cả chức năng
- Click "Simulate Registration" để test event
- Click "Debug Info" để xem thông tin chi tiết

### 3. Debug trong Console

```javascript
// Chạy tất cả tests
testSocket()

// Simulate registration event
simulateRegistration()

// Simulate status update
simulateStatusUpdate()

// Xem debug info
getSocketDebugInfo()
```

## Các tính năng chính

### Auto-Reconnection
- Tự động reconnect khi mất kết nối
- Exponential backoff (1s, 2s, 4s, 8s, 16s)
- Tối đa 5 lần thử reconnect

### State Persistence
- Lưu registrations vào localStorage
- Khôi phục state khi quay lại trang
- Tự động cleanup data cũ (>5 phút)

### Page Navigation Handling
- Duy trì connection khi navigate giữa các trang
- Reconnect khi user quay lại tab
- Lưu state trước khi rời trang

### Event Management
- Quản lý tất cả socket listeners
- Tự động reconnect listeners sau khi reconnect
- Support multiple channels và events

## Cấu trúc Files

```
public/js/
├── socket-manager.js      # SocketManager class chính
├── socket-test.js         # Test utility
└── ...

resources/views/
├── layouts/
│   └── app.blade.php      # Load SocketManager globally
├── backend/service-registrations/
│   └── index.blade.php    # Sử dụng SocketManager
└── socket-test.blade.php  # Test page
```

## Events được handle

1. **registration.created** - Khi có đăng ký mới
2. **status.updated** - Khi cập nhật trạng thái
3. **status.deleted** - Khi xóa registration

## Channels được sử dụng

1. **laravel_database_registrations** - Cho registration events
2. **laravel_database_status** - Cho status events

## Troubleshooting

### Socket không kết nối được
1. Kiểm tra Laravel Echo Server có chạy không: `laravel-echo-server start`
2. Kiểm tra port 6001 có mở không
3. Kiểm tra console có lỗi gì không

### State không được lưu
1. Kiểm tra localStorage có bị disable không
2. Kiểm tra browser có support localStorage không
3. Kiểm tra data có quá lớn không (>5MB)

### Events không được nhận
1. Kiểm tra user có permission xem department không
2. Kiểm tra channel và event name có đúng không
3. Kiểm tra SocketManager có connected không

## Performance Notes

- SocketManager chỉ tạo 1 connection duy nhất
- State được lưu trong memory và localStorage
- Auto cleanup data cũ để tránh memory leak
- Exponential backoff để tránh spam reconnect

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Dependencies

- Socket.IO Client
- Laravel Echo
- LocalStorage API
- Page Visibility API (optional)

## Future Enhancements

1. **WebSocket Fallback:** Fallback sang WebSocket nếu Socket.IO fail
2. **Offline Support:** Cache events khi offline, sync khi online
3. **Push Notifications:** Thêm push notifications cho mobile
4. **Analytics:** Track socket connection metrics
5. **Multi-tab Sync:** Sync state giữa các tab
