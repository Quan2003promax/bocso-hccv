# Hệ thống Bốc số thứ tự - Tính năng hoàn chỉnh

## Tổng quan
Hệ thống đã được thiết kế với 2 luồng hoạt động riêng biệt:
1. **Trang chủ công khai**: Cho phép mọi người đăng ký dịch vụ và xem danh sách số thứ tự
2. **Dashboard bảo mật**: Hiển thị thống kê và danh sách đăng ký gần đây
3. **Trang Backend**: Quản lý phòng ban và đăng ký dịch vụ với giao diện Tailwind CSS đồng bộ

## Cách hoạt động

### 1. Trang chủ (Home) - `/`
- **Ai cũng có thể truy cập**: Không cần đăng nhập
- **Form đăng ký dịch vụ**: Công khai cho mọi người
- **Danh sách số thứ tự**: Hiển thị 10 đăng ký đang chờ xử lý
- **Navigation bar**: Có nút đăng nhập/đăng ký tài khoản
- **Hero section**: Giới thiệu hệ thống

### 2. Dashboard - `/dashboard`
- **Chỉ người dùng đã đăng nhập mới truy cập được**
- **Thống kê tổng quan**: Số phòng ban, tổng đăng ký, chờ xử lý, hoàn thành
- **Danh sách đăng ký gần đây**: 5 đăng ký mới nhất

### 3. Trang Backend
- **Quản lý phòng ban** (`/admin/departments`): CRUD phòng ban với Tailwind CSS
- **Quản lý đăng ký dịch vụ** (`/admin/service-registrations`): Quản lý đăng ký với Tailwind CSS
- **Giao diện đồng bộ**: Sử dụng cùng Tailwind CSS như Dashboard
- **SweetAlert2**: Confirm dialogs đẹp mắt cho các thao tác xóa
- **Cập nhật trạng thái**: Select option để quản trị cập nhật status cho khách hàng

### 4. Hệ thống trạng thái
Hệ thống có 4 trạng thái chính:
- **Chờ xử lý** (`pending`): Màu vàng - Đăng ký mới được tạo
- **Đã tiếp nhận** (`received`): Màu xanh dương - Đã được tiếp nhận bởi nhân viên
- **Đang xử lý** (`processing`): Màu chàm - Đang được xử lý
- **Hoàn thành** (`completed`): Màu xanh lá - Đã hoàn thành xử lý

### 5. Phân quyền theo vai trò

#### Người dùng chưa đăng nhập:
- ✅ Truy cập trang chủ
- ✅ Đăng ký dịch vụ từ trang chủ
- ✅ Xem danh sách số thứ tự
- ❌ Không thể vào Dashboard
- ❌ Không thể vào trang backend

#### Người dùng thường (đã đăng nhập):
- ✅ Truy cập trang chủ
- ✅ Đăng ký dịch vụ từ trang chủ
- ✅ Xem danh sách số thứ tự
- ✅ Vào Dashboard
- ✅ Xem thống kê tổng quan
- ✅ Xem danh sách đăng ký gần đây
- ❌ Không thể vào trang backend

#### Super-Admin:
- ✅ Tất cả quyền của người dùng thường
- ✅ Quản lý phòng ban (từ admin routes)
- ✅ Quản lý tất cả đăng ký dịch vụ (từ admin routes)
- ✅ Cập nhật trạng thái đăng ký dịch vụ

## Cấu trúc Routes

### Routes công khai:
- `GET /` - Trang chủ (HomeController@index)
- `POST /service-register` - Đăng ký dịch vụ từ trang chủ (HomeController@register)

### Routes cần đăng nhập:
- `GET /dashboard` - Dashboard (cần auth)
- `GET /login` - Trang đăng nhập
- `POST /login` - Xử lý đăng nhập
- `GET /register` - Trang đăng ký tài khoản

### Routes Admin (cần role admin|Super-Admin):
- `GET /admin/departments` - Quản lý phòng ban
- `GET /admin/service-registrations` - Danh sách đăng ký dịch vụ
- `GET /admin/service-registrations/create` - Form tạo đăng ký mới (chỉ Super-Admin)
- `POST /admin/service-registrations` - Lưu đăng ký mới từ Dashboard
- `PATCH /admin/service-registrations/{id}/status` - Cập nhật trạng thái đăng ký

## Giao diện

### Trang chủ:
- Navigation bar với nút đăng nhập/đăng ký
- Hero section giới thiệu
- Form đăng ký dịch vụ (Bootstrap 5)
- Danh sách số thứ tự đang chờ
- Footer

### Dashboard:
- Header với tên người dùng
- Thống kê tổng quan (4 cards): Phòng ban, Tổng đăng ký, Chờ xử lý, Hoàn thành
- Danh sách đăng ký gần đây
- Navigation menu bên trái

### Trang Backend:
- **Layout Tailwind CSS**: Sử dụng cùng giao diện như Dashboard
- **Cards và Tables**: Thiết kế nhất quán với Dashboard
- **SweetAlert2**: Confirm dialogs đẹp mắt
- **Font Awesome**: Icons cho các nút và trạng thái
- **Responsive**: Tương thích với mọi thiết bị
- **Hover effects**: Tương tác mượt mà
- **Status Select**: Dropdown để cập nhật trạng thái real-time

### Trang tạo đăng ký mới (Dashboard):
- Form đăng ký dịch vụ (Tailwind CSS)
- Validation với thông báo lỗi tiếng Việt
- Nút quay lại Dashboard

## Chức năng cập nhật trạng thái

### Cách hoạt động:
1. **Quản trị viên** vào trang "Quản lý đăng ký dịch vụ"
2. **Chọn trạng thái mới** từ dropdown trong cột "Trạng thái"
3. **Hệ thống tự động** gửi request cập nhật
4. **Hiển thị thông báo** thành công/thất bại
5. **Cập nhật real-time** không cần reload trang

### API Endpoint:
- **URL**: `PATCH /admin/service-registrations/{id}/status`
- **Request Body**: `{"status": "new_status"}`
- **Response**: JSON với thông tin cập nhật
- **Validation**: Chỉ cho phép 4 trạng thái hợp lệ

### Tính năng:
- ✅ **Real-time update**: Không cần reload trang
- ✅ **Loading state**: Hiển thị trạng thái đang xử lý
- ✅ **Error handling**: Xử lý lỗi và khôi phục giá trị cũ
- ✅ **Success feedback**: Thông báo thành công với SweetAlert2
- ✅ **Log tracking**: Ghi log mọi thay đổi trạng thái

## Bảo mật

### Middleware:
- `auth` - Bảo vệ Dashboard và admin routes
- `role:admin|Super-Admin` - Bảo vệ admin routes

### Validation:
- Kiểm tra dữ liệu đầu vào chặt chẽ
- Thông báo lỗi tiếng Việt
- Kiểm tra phòng ban có hoạt động hay không
- Validate trạng thái chỉ cho phép 4 giá trị hợp lệ

### Session:
- Regenerate session sau mỗi lần đăng nhập
- CSRF protection cho tất cả forms

## Sử dụng

### Để đăng ký dịch vụ:
1. **Cách 1 (Công khai)**: Vào trang chủ `/` và sử dụng form đăng ký
2. **Cách 2 (Super-Admin)**: Đăng nhập → Admin routes → Quản lý đăng ký dịch vụ

### Để quản lý hệ thống:
1. Đăng nhập với tài khoản Super-Admin
2. Sử dụng các admin routes trực tiếp:
   - `/admin/departments` - Quản lý phòng ban
   - `/admin/service-registrations` - Quản lý đăng ký dịch vụ

### Để cập nhật trạng thái:
1. Vào trang "Quản lý đăng ký dịch vụ"
2. Chọn trạng thái mới từ dropdown trong cột "Trạng thái"
3. Hệ thống tự động cập nhật và hiển thị thông báo

## Lưu ý quan trọng

- **Trang chủ vẫn hoạt động bình thường** như trước
- **Dashboard đã được đơn giản hóa**: Chỉ hiển thị thống kê và danh sách đăng ký gần đây
- **Các chức năng quản lý**: Chỉ có thể truy cập thông qua admin routes
- **Hệ thống vừa công khai vừa bảo mật** theo từng vai trò
- **Giao diện Dashboard gọn gàng hơn**: Tập trung vào thông tin hiển thị
- **Trang Backend đã được đồng bộ**: Sử dụng cùng Tailwind CSS như Dashboard
- **Giao diện nhất quán**: Tất cả trang backend có cùng style và layout
- **CSS và JS đã được tối ưu**: Loại bỏ Bootstrap, chỉ sử dụng Tailwind CSS
- **Chức năng cập nhật trạng thái**: Select option real-time để quản trị cập nhật status
- **4 trạng thái chuẩn**: Chờ xử lý, Đã tiếp nhận, Đang xử lý, Hoàn thành
