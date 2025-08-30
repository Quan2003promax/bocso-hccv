# Hệ Thống Quản Lý Quyền và Vai Trò

## Tổng Quan

Hệ thống này được xây dựng dựa trên package `spatie/laravel-permission` với các tính năng mở rộng:

- Quản lý vai trò (Roles)
- Quản lý quyền (Permissions) 
- Gán quyền cho vai trò
- Gán vai trò và quyền trực tiếp cho người dùng
- Kiểm tra quyền theo phòng ban
- Middleware tùy chỉnh để kiểm tra quyền và phòng ban

## Cài Đặt và Thiết Lập

### 1. Chạy Migration

```bash
php artisan migrate
```

### 2. Chạy Seeder

```bash
php artisan db:seed
```

Seeder sẽ tạo:
- Các permission cơ bản
- Vai trò: Super-Admin, Admin, Manager, Staff
- User admin mặc định

### 3. Cấu Trúc Database

#### Bảng `users`
- `department_id`: Liên kết với phòng ban

#### Bảng `departments`
- `name`: Tên phòng ban
- `description`: Mô tả
- `status`: Trạng thái (active/inactive)

#### Bảng `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`
- Được tạo bởi spatie/laravel-permission

## Sử Dụng

### 1. Kiểm Tra Quyền Cơ Bản

```php
// Kiểm tra vai trò
if ($user->hasRole('Admin')) {
    // User có vai trò Admin
}

// Kiểm tra quyền
if ($user->hasPermissionTo('user-create')) {
    // User có quyền tạo user
}

// Kiểm tra quyền từ vai trò
if ($user->can('user-edit')) {
    // User có quyền chỉnh sửa user
}
```

### 2. Kiểm Tra Quyền Theo Phòng Ban

```php
// Kiểm tra user có thuộc phòng ban cụ thể không
if ($user->belongsToDepartment('IT')) {
    // User thuộc phòng ban IT
}

// Kiểm tra quyền trong phòng ban cụ thể
if ($user->hasPermissionInDepartment('user-edit', 'IT')) {
    // User có quyền user-edit VÀ thuộc phòng ban IT
}
```

### 3. Sử Dụng Middleware

#### Middleware Cơ Bản
```php
// Kiểm tra quyền
Route::middleware('permission:user-list')->group(function () {
    // Routes yêu cầu quyền user-list
});

// Kiểm tra vai trò
Route::middleware('role:Admin')->group(function () {
    // Routes yêu cầu vai trò Admin
});
```

#### Middleware Tùy Chỉnh
```php
// Kiểm tra quyền và phòng ban
Route::middleware('check.department.permission:user-edit,IT')->group(function () {
    // Routes yêu cầu quyền user-edit VÀ thuộc phòng ban IT
});

// Chỉ kiểm tra phòng ban
Route::middleware('check.department.permission:null,HR')->group(function () {
    // Routes chỉ dành cho phòng ban HR
});

// Chỉ kiểm tra quyền
Route::middleware('check.department.permission:user-delete')->group(function () {
    // Routes yêu cầu quyền user-delete
});
```

### 4. Trong Controller

```php
public function __construct()
{
    // Kiểm tra quyền cơ bản
    $this->middleware('permission:user-list');
    
    // Kiểm tra quyền và phòng ban cụ thể
    $this->middleware('check.department.permission:user-edit,IT')->only(['edit', 'update']);
    
    // Kiểm tra chỉ phòng ban
    $this->middleware('check.department.permission:null,HR')->only(['hrOnly']);
}
```

### 5. Trong Blade Templates

```php
{{-- Kiểm tra quyền --}}
@can('user-create')
    <a href="{{ route('users.create') }}">Tạo User</a>
@endcan

{{-- Kiểm tra vai trò --}}
@role('Admin')
    <div>Nội dung chỉ dành cho Admin</div>
@endrole

{{-- Kiểm tra quyền hoặc vai trò --}}
@hasanyrole('Admin|Manager')
    <div>Nội dung cho Admin hoặc Manager</div>
@endhasanyrole
```

## Quản Lý Quyền

### 1. Tạo Permission Mới

```php
use Spatie\Permission\Models\Permission;

Permission::create(['name' => 'new-permission']);
```

### 2. Tạo Role Mới

```php
use Spatie\Permission\Models\Role;

$role = Role::create(['name' => 'NewRole']);
$role->givePermissionTo('user-list', 'user-create');
```

### 3. Gán Quyền Cho User

```php
// Gán vai trò
$user->assignRole('Admin');

// Gán quyền trực tiếp
$user->givePermissionTo('user-edit');

// Đồng bộ quyền (xóa tất cả và gán mới)
$user->syncPermissions(['user-list', 'user-create']);
```

## Các Permission Mặc Định

### User Management
- `user-list`: Xem danh sách user
- `user-create`: Tạo user mới
- `user-edit`: Chỉnh sửa user
- `user-delete`: Xóa user
- `user-show`: Xem chi tiết user

### Role Management
- `role-list`: Xem danh sách vai trò
- `role-create`: Tạo vai trò mới
- `role-edit`: Chỉnh sửa vai trò
- `role-delete`: Xóa vai trò
- `role-show`: Xem chi tiết vai trò

### Permission Management
- `permission-list`: Xem danh sách quyền
- `permission-create`: Tạo quyền mới
- `permission-edit`: Chỉnh sửa quyền
- `permission-delete`: Xóa quyền
- `permission-show`: Xem chi tiết quyền

### Department Management
- `department-list`: Xem danh sách phòng ban
- `department-create`: Tạo phòng ban mới
- `department-edit`: Chỉnh sửa phòng ban
- `department-delete`: Xóa phòng ban
- `department-show`: Xem chi tiết phòng ban

## Vai Trò Mặc Định

### Super-Admin
- Có tất cả quyền
- Không thể bị xóa hoặc chỉnh sửa

### Admin
- Quản lý user, role, permission, department
- Quản lý service registration
- Không có quyền xóa role/permission

### Manager
- Xem danh sách user và department
- Quản lý service registration
- Không có quyền tạo/xóa

### Staff
- Chỉ xem service registration
- Quyền hạn thấp nhất

## Demo Routes

Để test hệ thống, sử dụng các routes sau:

```
GET /example - Kiểm tra quyền user-list
GET /example/{id}/edit - Kiểm tra quyền user-edit + phòng ban IT
PUT /example/{id} - Kiểm tra quyền user-edit + phòng ban IT
DELETE /example/{id} - Kiểm tra quyền user-delete
GET /example/hr-only - Chỉ dành cho phòng ban HR
GET /example/check-permissions - Kiểm tra quyền của user hiện tại
```

## Lưu Ý

1. **Cache**: Spatie Permission sử dụng cache để tối ưu hiệu suất. Khi thay đổi quyền, cache sẽ tự động được xóa.

2. **Guard**: Mặc định sử dụng guard 'web'. Có thể thay đổi trong config/permission.php.

3. **Teams**: Tính năng teams chưa được bật. Nếu cần, hãy cập nhật config và migration.

4. **Validation**: Luôn validate dữ liệu đầu vào khi tạo/sửa quyền và vai trò.

5. **Security**: Không bao giờ expose quyền nhạy cảm ra frontend nếu không cần thiết.

## Troubleshooting

### Lỗi "Permission not found"
- Kiểm tra tên permission có đúng không
- Chạy `php artisan permission:cache-reset`

### Lỗi "Role not found"
- Kiểm tra tên role có đúng không
- Chạy `php artisan permission:cache-reset`

### Quyền không hoạt động
- Kiểm tra user có được gán role/permission chưa
- Kiểm tra middleware có đúng không
- Chạy `php artisan permission:cache-reset`
