# API Authentication Demo

## Cách test API Authentication

### 1. Đăng nhập để lấy token

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

**Response:**
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
        "token": "1|abcdef123456789...",
        "token_type": "Bearer"
    }
}
```

### 2. Sử dụng token để đăng ký dịch vụ

```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -d '{
    "full_name": "Nguyễn Văn A",
    "birth_year": 1990,
    "identity_number": "123456789",
    "email": "nguyenvana@example.com",
    "phone": "0123456789",
    "department_id": 1
  }'
```

### 3. Lấy thông tin user hiện tại

```bash
curl -X GET http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer 1|abcdef123456789..."
```

### 4. Lấy thống kê (cần authentication)

```bash
curl -X GET http://localhost:8000/api/v1/statistics \
  -H "Authorization: Bearer 1|abcdef123456789..."
```

### 5. Đăng xuất

```bash
curl -X POST http://localhost:8000/api/v1/auth/logout \
  -H "Authorization: Bearer 1|abcdef123456789..."
```

## Test với Postman

### Collection Import
```json
{
    "info": {
        "name": "Queue System API",
        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    },
    "item": [
        {
            "name": "Auth Login",
            "request": {
                "method": "POST",
                "header": [
                    {
                        "key": "Content-Type",
                        "value": "application/json"
                    }
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n    \"email\": \"admin@example.com\",\n    \"password\": \"password\"\n}"
                },
                "url": {
                    "raw": "{{base_url}}/api/v1/auth/login",
                    "host": ["{{base_url}}"],
                    "path": ["api", "v1", "auth", "login"]
                }
            }
        },
        {
            "name": "Get User Info",
            "request": {
                "method": "GET",
                "header": [
                    {
                        "key": "Authorization",
                        "value": "Bearer {{token}}"
                    }
                ],
                "url": {
                    "raw": "{{base_url}}/api/v1/auth/me",
                    "host": ["{{base_url}}"],
                    "path": ["api", "v1", "auth", "me"]
                }
            }
        },
        {
            "name": "Register Service",
            "request": {
                "method": "POST",
                "header": [
                    {
                        "key": "Content-Type",
                        "value": "application/json"
                    },
                    {
                        "key": "Authorization",
                        "value": "Bearer {{token}}"
                    }
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n    \"full_name\": \"Nguyễn Văn A\",\n    \"birth_year\": 1990,\n    \"identity_number\": \"123456789\",\n    \"email\": \"nguyenvana@example.com\",\n    \"phone\": \"0123456789\",\n    \"department_id\": 1\n}"
                },
                "url": {
                    "raw": "{{base_url}}/api/v1/register",
                    "host": ["{{base_url}}"],
                    "path": ["api", "v1", "register"]
                }
            }
        }
    ],
    "variable": [
        {
            "key": "base_url",
            "value": "http://localhost:8000"
        },
        {
            "key": "token",
            "value": ""
        }
    ]
}
```

## JavaScript Example

```javascript
// API Base URL
const API_BASE = 'http://localhost:8000/api/v1';

// Login function
async function login(email, password) {
    const response = await fetch(`${API_BASE}/auth/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password })
    });
    
    const data = await response.json();
    
    if (data.success) {
        // Store token
        localStorage.setItem('api_token', data.data.token);
        return data.data;
    } else {
        throw new Error(data.message);
    }
}

// Register service function
async function registerService(serviceData) {
    const token = localStorage.getItem('api_token');
    
    const response = await fetch(`${API_BASE}/register`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(serviceData)
    });
    
    const data = await response.json();
    
    if (!data.success) {
        throw new Error(data.message);
    }
    
    return data.data;
}

// Usage
login('admin@example.com', 'password')
    .then(user => {
        console.log('Logged in:', user);
        
        return registerService({
            full_name: 'Nguyễn Văn A',
            birth_year: 1990,
            identity_number: '123456789',
            email: 'nguyenvana@example.com',
            phone: '0123456789',
            department_id: 1
        });
    })
    .then(result => {
        console.log('Registration successful:', result);
    })
    .catch(error => {
        console.error('Error:', error);
    });
```

## Error Handling

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 422 Validation Error
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["Email là bắt buộc"],
        "password": ["Mật khẩu phải có ít nhất 6 ký tự"]
    }
}
```

### 500 Server Error
```json
{
    "success": false,
    "message": "Có lỗi xảy ra khi đăng nhập"
}
```
