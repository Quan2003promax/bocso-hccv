# Hệ thống bốc số thứ tự - Dịch vụ hành chính công

## Mô tả
Hệ thống bốc số thứ tự để làm dịch vụ hành chính công, tương tự như hệ thống cấp số thứ tự khám bệnh tại bệnh viện.

## Tính năng chính

### 1. Trang chủ cho người dùng
- Form đăng ký dịch vụ với các thông tin:
  - Họ và tên
  - Năm sinh
  - Số căn cước công dân
  - Chọn phòng ban cần làm dịch vụ
- Hiển thị danh sách số thứ tự đang chờ
- Tự động cấp số thứ tự khi đăng ký thành công

### 2. Trang Admin
- **Quản lý phòng ban:**
  - Thêm, sửa, xóa phòng ban
  - Cập nhật trạng thái hoạt động
  - Mô tả chi tiết về từng phòng ban

- **Quản lý đăng ký dịch vụ:**
  - Xem danh sách tất cả đăng ký
  - Cập nhật trạng thái xử lý:
    - Chờ xử lý
    - Đang xử lý
    - Đã xử lý
    - Đã hủy
  - Thêm ghi chú cho từng đăng ký
  - Xem chi tiết thông tin đăng ký

### 3. Dashboard
- Thống kê tổng quan:
  - Tổng số phòng ban
  - Tổng số đăng ký
  - Số đăng ký đang chờ
  - Số đăng ký đã xử lý
- Danh sách đăng ký gần đây

## Cài đặt và chạy

### 1. Cài đặt dependencies
```bash
composer install
npm install
```

### 2. Cấu hình môi trường
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Cấu hình database trong file .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Chạy migration và seeder
```bash
php artisan migrate
php artisan db:seed
```

### 5. Chạy ứng dụng
```bash
php artisan serve
```

## Cấu trúc database

### Bảng `departments`
- `id`: ID phòng ban
- `name`: Tên phòng ban
- `description`: Mô tả phòng ban
- `status`: Trạng thái (active/inactive)
- `created_at`, `updated_at`: Timestamp

### Bảng `service_registrations`
- `id`: ID đăng ký
- `full_name`: Họ và tên
- `birth_year`: Năm sinh
- `identity_number`: Số căn cước công dân
- `department_id`: ID phòng ban (foreign key)
- `queue_number`: Số thứ tự
- `status`: Trạng thái xử lý
- `notes`: Ghi chú
- `created_at`, `updated_at`: Timestamp

## Quyền người dùng

Hệ thống sử dụng Spatie Laravel Permission để quản lý quyền:

### Quyền phòng ban
- `department-menu`: Menu phòng ban
- `department-list`: Xem danh sách phòng ban
- `department-create`: Tạo phòng ban mới
- `department-edit`: Chỉnh sửa phòng ban
- `department-delete`: Xóa phòng ban

### Quyền đăng ký dịch vụ
- `service-registration-menu`: Menu đăng ký dịch vụ
- `service-registration-list`: Xem danh sách đăng ký
- `service-registration-show`: Xem chi tiết đăng ký
- `service-registration-update-status`: Cập nhật trạng thái
- `service-registration-delete`: Xóa đăng ký

## Định dạng số thứ tự

Số thứ tự được tạo theo format: `{Tên phòng ban}-{Ngày tháng năm}-{Số thứ tự}`

Ví dụ: `Phòng Tài chính - Kế toán-20240101-001`

## API Endpoints

### Trang chủ
- `GET /`: Trang chủ với form đăng ký
- `POST /register`: Đăng ký dịch vụ mới

### Admin (yêu cầu đăng nhập và quyền admin)
- `GET /admin/departments`: Danh sách phòng ban
- `GET /admin/departments/create`: Form tạo phòng ban
- `POST /admin/departments`: Lưu phòng ban mới
- `GET /admin/departments/{id}/edit`: Form chỉnh sửa phòng ban
- `PUT /admin/departments/{id}`: Cập nhật phòng ban
- `DELETE /admin/departments/{id}`: Xóa phòng ban

- `GET /admin/service-registrations`: Danh sách đăng ký dịch vụ
- `GET /admin/service-registrations/{id}`: Chi tiết đăng ký
- `PATCH /admin/service-registrations/{id}/status`: Cập nhật trạng thái
- `DELETE /admin/service-registrations/{id}`: Xóa đăng ký

## Giao diện

### Trang chủ
- Thiết kế responsive với Bootstrap 5
- Form đăng ký với validation
- Bảng hiển thị số thứ tự đang chờ
- Thông báo thành công/lỗi

### Trang Admin
- Giao diện quản trị với AdminLTE
- Bảng dữ liệu với phân trang
- Form thêm/sửa với validation
- Modal xác nhận xóa

## Bảo mật

- Sử dụng middleware `auth` cho các trang admin
- Sử dụng middleware `role:admin` để kiểm tra quyền
- CSRF protection cho tất cả form
- Validation dữ liệu đầu vào
- Sanitize dữ liệu trước khi lưu database

## Tùy chỉnh

### Thay đổi trạng thái
Có thể thay đổi các trạng thái trong model `ServiceRegistration`:
```php
protected $casts = [
    'status' => 'string',
];

public function getStatusTextAttribute()
{
    $statuses = [
        'pending' => 'Chờ xử lý',
        'processing' => 'Đang xử lý',
        'completed' => 'Đã xử lý',
        'cancelled' => 'Đã hủy'
    ];
    
    return $statuses[$this->status] ?? $this->status;
}
```

### Thay đổi format số thứ tự
Có thể tùy chỉnh format trong method `generateQueueNumber` của `HomeController`.

## Hỗ trợ

Nếu có vấn đề hoặc cần hỗ trợ, vui lòng liên hệ:
- Email: support@example.com
- Phone: 0123-456-789

## License

Dự án này được phát hành dưới MIT License.
