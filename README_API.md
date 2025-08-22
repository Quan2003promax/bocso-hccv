# API Documentation - Hệ thống bốc số thứ tự

## Tổng quan
Hệ thống cung cấp cả **Web Interface** và **REST API** để quản lý dịch vụ hành chính công.

## Base URL
```
http://your-domain.com/api/v1
```

## Authentication
Hiện tại API không yêu cầu authentication. Trong tương lai có thể thêm JWT hoặc API Key.

## Endpoints

### 1. Đăng ký dịch vụ
**POST** `/register`

**Request Body:**
```json
{
    "full_name": "Nguyễn Văn A",
    "birth_year": 1990,
    "identity_number": "123456789",
    "department_id": 1
}
```

**Response Success (201):**
```json
{
    "success": true,
    "message": "Đăng ký thành công",
    "data": {
        "registration": {
            "id": 1,
            "queue_number": "001",
            "full_name": "Nguyễn Văn A",
            "birth_year": 1990,
            "identity_number": "123456789",
            "status": "pending",
            "status_text": "Chờ xử lý",
            "department": {
                "id": 1,
                "name": "Phòng Tài chính - Kế toán",
                "description": "Xử lý các vấn đề tài chính"
            },
            "created_at": "2024-01-15 10:30:00"
        },
        "estimated_wait_time": "5 phút"
    }
}
```

**Response Error (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "full_name": ["Vui lòng nhập họ và tên"],
        "department_id": ["Vui lòng chọn phòng ban"]
    }
}
```

### 2. Lấy danh sách phòng ban
**GET** `/departments`

**Response Success (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Phòng Tài chính - Kế toán",
            "description": "Xử lý các vấn đề tài chính",
            "status": "active",
            "created_at": "2024-01-15 10:00:00"
        }
    ]
}
```

### 3. Lấy trạng thái hàng đợi
**GET** `/queue-status?department_id=1` (department_id là tùy chọn)

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "pending_count": 5,
        "registrations": [
            {
                "id": 1,
                "queue_number": "001",
                "full_name": "Nguyễn Văn A",
                "status": "pending",
                "department": {
                    "id": 1,
                    "name": "Phòng Tài chính - Kế toán"
                },
                "created_at": "2024-01-15 10:30:00"
            }
        ],
        "last_updated": "2024-01-15T10:35:00.000000Z"
    }
}
```

### 4. Kiểm tra số thứ tự
**POST** `/check-queue`

**Request Body:**
```json
{
    "queue_number": "001"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "queue_number": "001",
        "full_name": "Nguyễn Văn A",
        "status": "pending",
        "status_text": "Chờ xử lý",
        "department": {
            "id": 1,
            "name": "Phòng Tài chính - Kế toán"
        },
        "created_at": "2024-01-15 10:30:00"
    }
}
```

### 5. Lấy thống kê tổng quan
**GET** `/statistics`

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "overview": {
            "total_departments": 8,
            "total_registrations": 25,
            "pending_registrations": 15,
            "completed_registrations": 8,
            "processing_registrations": 2
        },
        "department_stats": [
            {
                "id": 1,
                "name": "Phòng Tài chính - Kế toán",
                "pending_registrations_count": 5
            }
        ],
        "last_updated": "2024-01-15T10:35:00.000000Z"
    }
}
```

## HTTP Status Codes
- **200** - Success
- **201** - Created (đăng ký thành công)
- **400** - Bad Request (phòng ban không hoạt động)
- **404** - Not Found (không tìm thấy số thứ tự)
- **422** - Validation Error
- **500** - Internal Server Error

## Error Response Format
```json
{
    "success": false,
    "message": "Mô tả lỗi",
    "errors": {
        "field_name": ["Chi tiết lỗi"]
    }
}
```

## Rate Limiting
Hiện tại chưa áp dụng rate limiting. Trong tương lai có thể thêm:
- 100 requests/minute cho đăng ký
- 1000 requests/minute cho các endpoint khác

## Testing
Sử dụng trang test API: `/api-test`

## Mobile App Integration
API này có thể được sử dụng để:
- Tạo mobile app đăng ký dịch vụ
- Tích hợp với hệ thống khác
- Webhook notifications
- Real-time updates

## Future Enhancements
- JWT Authentication
- WebSocket cho real-time updates
- Push notifications
- File upload (đính kèm tài liệu)
- SMS/Email confirmations
