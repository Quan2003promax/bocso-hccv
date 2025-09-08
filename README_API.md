# API Documentation - Hệ thống bốc số thứ tự

## Tổng quan
Hệ thống cung cấp cả **Web Interface** và **REST API** để quản lý dịch vụ hành chính công.

## Base URL
```
http://your-domain.com/api/v1
```

## Authentication
API sử dụng **Laravel Sanctum** để xác thực. Có 2 loại routes:

### Public Routes (Không cần authentication)
- `GET /departments` - Lấy danh sách phòng ban
- `GET /queue-status` - Lấy trạng thái hàng đợi
- `POST /auth/login` - Đăng nhập để lấy token

### Protected Routes (Cần authentication)
- `POST /register` - Đăng ký dịch vụ
- `POST /check-queue` - Kiểm tra số thứ tự
- `GET /statistics` - Thống kê tổng quan
- `POST /auth/logout` - Đăng xuất
- `GET /auth/me` - Lấy thông tin user hiện tại

### Cách sử dụng Authentication

1. **Đăng nhập để lấy token:**
```bash
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}
```

2. **Sử dụng token trong các request:**
```bash
Authorization: Bearer {your-token-here}
```

3. **Đăng xuất:**
```bash
POST /api/v1/auth/logout
Authorization: Bearer {your-token-here}
```

## Endpoints

### Authentication Endpoints

#### 1. Đăng nhập
**POST** `/auth/login`

**Request Body:**
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Đăng nhập thành công",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "roles": ["Super-Admin"],
            "permissions": ["user-list", "user-create"],
            "departments": ["IT"]
        },
        "token": "1|abcdef123456...",
        "token_type": "Bearer"
    }
}
```

**Response Error (401):**
```json
{
    "success": false,
    "message": "Email hoặc mật khẩu không đúng"
}
```

#### 2. Lấy thông tin user hiện tại
**GET** `/auth/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "roles": ["Super-Admin"],
            "permissions": ["user-list", "user-create"],
            "departments": ["IT"],
            "created_at": "2024-01-15T10:00:00.000000Z"
        }
    }
}
```

#### 3. Đăng xuất
**POST** `/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Đăng xuất thành công"
}
```

#### 4. Đăng xuất khỏi tất cả thiết bị
**POST** `/auth/logout-all`

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Đăng xuất khỏi tất cả thiết bị thành công"
}
```

#### 5. Làm mới token
**POST** `/auth/refresh`

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Token đã được làm mới",
    "data": {
        "token": "2|xyz789...",
        "token_type": "Bearer"
    }
}
```

### Service Endpoints

#### 1. Đăng ký dịch vụ
**POST** `/register`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

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
