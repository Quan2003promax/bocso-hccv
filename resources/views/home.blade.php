<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống bốc số thứ tự - Dịch vụ hành chính công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --stack-width: 1300px;
            /* max width form */
            --search-max: 680px;
            /* max width search pill */
            --overlap: 600px;
            /* card thò xuống dưới bao nhiêu */
        }

        /* ===== HERO ===== */
        .hero-section {
            background-image: linear-gradient(115deg, rgba(6, 28, 61, .85), rgba(6, 28, 61, .35)),
                url('https://images.unsplash.com/photo-1519677100203-a0e668c92439?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            padding: 120px 0 calc(64px + var(--overlap));
            color: #fff;
            position: relative;
            overflow: visible;
            padding: 120px 0 120px;
            z-index: 1;
        }

        .hero-title {
            font-size: 3.6rem;
            font-weight: 600;
            font-family: Figtree;
        }

        .hero-sub {
            font-size: 18px;
            opacity: .9;
        }

        /* ===== SEARCH ===== */
        .hero-search {
            margin-top: 24px;
            position: relative;
            z-index: 2;
        }

        .hero-search .search-box {
            display: flex;
            align-items: center;
            width: min(var(--search-max), 100%);
            height: 66px;
            border-radius: 999px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(0, 0, 0, .22);
        }

        .search-box input {
            flex: 1;
            height: 100%;
            padding: 0 26px;
            font-size: 18px;
            border: none;
            outline: none;
        }

        .search-box input::placeholder {
            color: #98a2b3;
        }

        .search-action,
        .search-clear {
            height: 66px;
            padding: 0 24px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform .06s ease, background-color .2s ease;
        }

        .search-action {
            color: #000;
        }

        .search-action:hover {
            background: #000;
            color: #fff
        }

        .search-clear {
            background: #E11D48;
            color: #fff;
            text-decoration: none;
        }

        .search-clear:hover {
            background: #be123c;
        }


        .reservation-card {
            position: absolute;
            left: 50%;
            bottom: calc(-1 * var(--overlap));
            transform: translateX(-50%);
            width: min(var(--stack-width), calc(100% - 32px));
            border-radius: 28px;
            background: #fff;
            box-shadow: 0 28px 70px rgba(2, 6, 23, .22);
            z-index: 5;
        }

        .reservation-card .card-header {
            background: #fff;
            border: 0;
            text-align: left;
        }

        .reservation-card h3 {
            font-weight: 800;
            color: #0b3a6b;
        }

        .form-section {
            background: #f2f4f7;
            padding: 64px 0;
            margin: 0;
        }

        .hero-section+.queue-section {
            margin-top: calc(var(--overlap) + 24px);
        }

        .reservation-card,
        .reservation-card .form-label,
        .reservation-card .form-text,
        .reservation-card .invalid-feedback {
            color: #000;
        }

        .reservation-card {
            min-height: 520px;
        }

        .queue-section {
            background: #f7f9fc;
            padding: 72px 0 80px;

            margin-top: calc(var(--overlap, 0px) + 24px);
        }

        .section-head {
            text-align: center;
            margin-bottom: 28px;
        }

        .section-head h2 {
            margin: 0;
            font-weight: 800;
            color: #0b3a6b;
            font-size: clamp(26px, 3.2vw, 40px);
            letter-spacing: .2px;
        }

        .section-head .sub {
            margin-top: 8px;
            color: #64748b;
        }

        /* Card phòng ban */
        .dept-card {
            border: 1px solid #e6eaf0;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 14px 28px rgba(2, 6, 23, .06);
            transition: transform .18s ease, box-shadow .18s ease;
            overflow: hidden;
        }

        .dept-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 22px 50px rgba(2, 6, 23, .10);
        }

        /* Header của card */
        .dept-card__header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 18px;
            border-bottom: 1px solid #eef1f5;
            font-weight: 800;
            color: #0b3a6b;
            background: #fff;
        }

        .dept-card__header i {
            color: #0b3a6b;
            font-size: 18px;
        }

        /* Bảng */
        .dept-card .table thead th {
            text-transform: none;
            font-weight: 700;
            color: #334155;
            background: #f8fafc;
            border-bottom: 1px solid #eef1f5;
        }

        .dept-card .table tbody td {
            border-color: #f1f5f9;
            font-size: 0.95rem;
        }

        /* THÊM: làm hàng form gọn hơn, nút ngang bằng input */
        .reservation-card .row {
            row-gap: 12px;
        }

        .reservation-card .col-md-6.d-flex.align-items-end {
            flex-direction: column;
            /* quan trọng: chuyển sang trục dọc */
            align-items: stretch !important;
            /* nút sẽ rộng bằng cột */
        }

        .reservation-card .col-md-6.d-flex.align-items-end>.btn {
            margin-top: auto;
            width: 100%;
            height: 56px;
            font-weight: 700;
            border-radius: 12px;
            line-height: 1.2;
            margin-bottom: auto;
        }

        .bg-primary {
            backdrop-filter: blur(12px) !important;
            background: rgba(1, 30, 65, .8) !important;
            border-bottom: 1px solid rgba(209, 214, 220, .6) !important;
        }

        .navbar {
            position: relative;
            /* tạo stacking context */
            z-index: 3000;
            /* cao hơn hero (1) và card (5) */
        }

        /* Bản thân dropdown menu cũng có z-index cao */
        .navbar .dropdown-menu {
            z-index: 4000 !important;
        }

        @media (min-width: 992px) {
            .navbar-nav .dropdown-menu {
                position: absolute !important;
            }
        }

        @media (max-width: 576px) {
            .hero-section+.queue-section {
                margin-top: calc(var(--overlap) + 16px);

                .reservation-card {
                    min-height: 580px;
                }

                .queue-section {
                    padding: 56px 0 64px;
                    margin-top: calc(var(--overlap, 0px) + 16px);
                }
            }
        }

        .fade-out {
            animation: fadeOut 0.8s forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: scale(1);
            }

            to {
                opacity: 0;
                transform: scale(.95);
                height: 0;
            }
        }

        .fade-in {
            animation: fadeIn 0.8s forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-clipboard-list me-2"></i>
                Hệ thống bốc số thứ tự
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Đăng nhập
                        </a>
                    </li>
                    @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i>
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    Dashboard
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative">
            <div class="row">
                <div class="col-lg-7 text-start">
                    <h1 class="hero-title mb-2">
                        <i class="fas fa-clipboard-list me-3"></i> Hệ thống bốc số thứ tự
                    </h1>
                    <p class="hero-sub mb-0">Dịch vụ hành chính công - Phục vụ nhân dân 24/7</p>

                    <!-- Ô SEARCH -->
                    <div class="hero-search mt-3">
                        <form action="{{ route('home') }}" method="GET" class="search-box" id="queue-search-form">
                            <input type="text" name="search" id="queue-search-input"
                                placeholder="Vui lòng nhập Số thứ tự"
                                value="{{ $searchQuery ?? '' }}" required>
                            <button type="submit" class="search-action" aria-label="Tìm kiếm">
                                <i class="fas fa-search"></i>
                            </button>

                        </form>
                    </div>
                </div>
            </div>
            <!-- Form Section -->
            <div class="reservation-card">
                <div class="card-header p-4">
                    <h3><i class="fas fa-user-plus me-2"></i>Đăng ký bốc số</h3>
                </div>

                <div class="card-body p-4">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><strong>Vui lòng sửa các lỗi sau:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form action="{{ route('service.register') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Họ và tên <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                    id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                                @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birth_year" class="form-label">
                                    <i class="fas fa-calendar me-2"></i>Năm sinh <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control @error('birth_year') is-invalid @enderror"
                                    id="birth_year" name="birth_year" value="{{ old('birth_year') }}"
                                    min="1900" max="{{ date('Y') + 1 }}" required>
                                @error('birth_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="identity_number" class="form-label">
                                    <i class="fas fa-id-card me-2"></i>Số căn cước công dân <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('identity_number') is-invalid @enderror"
                                    id="identity_number" name="identity_number" value="{{ old('identity_number') }}" required>
                                @error('identity_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">
                                    <i class="fas fa-building me-2"></i>Phòng ban <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('department_id') is-invalid @enderror"
                                    id="department_id" name="department_id" required>
                                    <option value="">Chọn phòng ban</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="example@email.com" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-2"></i>Số điện thoại <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone') }}"
                                    placeholder="0123456789" required>
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="document_file" class="form-label">
                                    <i class="fas fa-file-upload me-2"></i>Tài liệu đính kèm
                                </label>
                                <input type="file" class="form-control @error('document_file') is-invalid @enderror"
                                    id="document_file" name="document_file"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Hỗ trợ: PDF, DOC, DOCX, JPG, PNG, GIF. Kích thước tối đa: 128MB
                                    </small>
                                </div>
                                @error('document_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-lg px-5 w-100">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Đăng ký dịch vụ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
        </div>
        </div>
        <!-- Modal: Kết quả tìm kiếm số thứ tự -->
        <div class="modal fade" id="queueResultModal" tabindex="-1" aria-labelledby="queueResultModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="queueResultModalLabel">Thông tin số thứ tự</h5>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <span id="queue-result-counter" class="text-muted small me-2 d-none">1/1</span>
                            <button type="button" id="queue-result-prev" class="btn btn-outline-secondary btn-sm" title="Trước">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" id="queue-result-next" class="btn btn-outline-secondary btn-sm" title="Sau">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div id="queue-result-loading" class="text-center d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="queue-result-content" class="d-none"></div>
                        <div id="queue-result-empty" class="text-muted text-center d-none">Không tìm thấy số thứ tự phù hợp</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Queue Section -->
        <section class="queue-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card queue-card">
                            <div class="card-header bg-success text-white text-center py-4">
                                <h3 class="mb-0">
                                    <i class="fas fa-list-ol me-2"></i>
                                    Danh sách số thứ tự đang chờ
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="row">
                                    @foreach($departments as $department)
                                    <div class="col-md-4 mb-4">
                                        <div class="department-queue">
                                            <h5 class="text-center mb-3 p-2 bg-light border rounded">
                                                <i class="fas fa-building me-2"></i>
                                                {{ $department->name }}
                                            </h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center" style="width: 30%">Số thứ tự</th>
                                                            <th style="width: 40%">Họ tên</th>
                                                            <th class="text-center" style="width: 30%">Trạng thái</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="queue-tbody-{{ $department->id }}" data-department-id="{{ $department->id }}">
                                                        @php
                                                        $departmentRegistrations = $pendingRegistrations->where('department_id', $department->id);
                                                        @endphp
                                                        @forelse($departmentRegistrations as $registration)
                                                        <tr data-id="{{ $registration->id }}">
                                                            <td class="text-center">
                                                                <span class="badge bg-primary fs-6">{{ $registration->queue_number }}</span>
                                                            </td>
                                                            <td class="text-truncate" title="{{ $registration->full_name }}">
                                                                {{ Str::limit($registration->full_name, 15) }}
                                                            </td>
                                                            <td class="text-center status-cell">
                                                                @switch($registration->status)
                                                                @case('pending') <span class="badge bg-warning text-dark">Chờ xử lý</span> @break
                                                                @case('received') <span class="badge bg-info text-dark">Đã tiếp nhận</span> @break
                                                                @case('processing') <span class="badge bg-primary text-white">Đang xử lý</span> @break
                                                                @case('completed') <span class="badge bg-success text-white">Hoàn thành</span> @break
                                                                @case('returned') <span class="badge bg-secondary text-white">Trả hồ sơ</span> @break
                                                                @default <span class="badge bg-secondary text-white">Không xác định</span>
                                                                @endswitch
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center py-3 text-muted">
                                                                <i class="fas fa-inbox fa-lg mb-2"></i>
                                                                <br><small>Chưa có đăng ký</small>
                                                            </td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-dark text-white text-center py-4">
            <div class="container">
                <p class="mb-0">&copy; © 2025 Thiết kế và xây dựng bởi VIETTECHKEY. All rights reserved.</p>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Socket.IO v2 từ Echo server (phải chạy ở :6001) -->
        <script src="http://localhost:6001/socket.io/socket.io.js"></script>

        <!-- Laravel Echo IIFE -->
        <script src="https://unpkg.com/laravel-echo@1.15.3/dist/echo.iife.js"></script>

        <script>
            // cho Echo dùng io toàn cục
            window.io = io;

            // Một số bản IIFE expose constructor khác nhau, bắt tất cả:
            const EchoCtor = (window.Echo && window.Echo.default) || window.Echo || window.LaravelEcho;

            const echo = new EchoCtor({
                broadcaster: 'socket.io',
                host: `${location.hostname}:6001`,
                transports: ['websocket', 'polling'],
            });
        </script>
        <script src="{{ asset('js/87cb0aedbef434af1d211ace28fe4ccc.js') }}"></script>

        <!-- <script>
        echo.channel('laravel_database_status')
            .listen('.status.updated', (e) => {
                upsertRow(e);
            });
    </script> -->

        <script>
            (function() {
                const form = document.getElementById('queue-search-form');
                const input = document.getElementById('queue-search-input');
                const modalEl = document.getElementById('queueResultModal');
                const modal = new bootstrap.Modal(modalEl);
                const loadingEl = document.getElementById('queue-result-loading');
                const contentEl = document.getElementById('queue-result-content');
                const emptyEl = document.getElementById('queue-result-empty');
                const prevBtn = document.getElementById('queue-result-prev');
                const nextBtn = document.getElementById('queue-result-next');
                const counterEl = document.getElementById('queue-result-counter');

                let results = [];
                let currentIndex = 0;

                function statusBadge(status) {
                    switch (status) {
                        case 'pending':
                            return '<span class="badge bg-warning text-dark">Chờ xử lý</span>';
                        case 'received':
                            return '<span class="badge bg-info text-dark">Đã tiếp nhận</span>';
                        case 'processing':
                            return '<span class="badge bg-primary">Đang xử lý</span>';
                        case 'completed':
                            return '<span class="badge bg-success">Hoàn thành</span>';
                        case 'returned':
                            return '<span class="badge bg-secondary">Trả hồ sơ</span>';
                        default:
                            return '<span class="badge bg-light text-dark">N/A</span>';
                    }
                }

                function render(index) {
                    if (!Array.isArray(results) || results.length === 0) return;
                    if (index < 0 || index >= results.length) return;
                    const d = results[index];
                    contentEl.innerHTML = `
            <div class="d-flex align-items-center mb-3">
              <div class="display-6 fw-bold me-3 text-primary">#${(d.queue_number||'').toString()}</div>
              <div>
                <div class="fw-bold">${(d.full_name||'')}</div>
                <div class="text-muted small">CCCD: ${(d.identity_number||'')}</div>
              </div>
            </div>
            <ul class="list-group mb-3">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Phòng ban
                <span class="fw-semibold">${(d.department||'')}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Trạng thái
                ${statusBadge(d.status)}
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Thời gian tạo
                <span>${(d.created_at||'')}</span>
              </li>
            </ul>
          `;
                    contentEl.classList.remove('d-none');
                    counterEl.textContent = `${index + 1}/${results.length}`;
                    counterEl.classList.toggle('d-none', results.length <= 1);
                    prevBtn.disabled = (index === 0);
                    nextBtn.disabled = (index >= results.length - 1);
                }

                prevBtn?.addEventListener('click', function() {
                    if (currentIndex > 0) {
                        currentIndex -= 1;
                        render(currentIndex);
                    }
                });
                nextBtn?.addEventListener('click', function() {
                    if (currentIndex < results.length - 1) {
                        currentIndex += 1;
                        render(currentIndex);
                    }
                });

                if (!form) return;

                form.addEventListener('submit', function(evt) {
                    // Chỉ bắt nếu người dùng nhập toàn là số (ưu tiên tìm theo Số thứ tự)
                    const query = (input.value || '').trim();
                    if (!query) return; // để mặc định reload trang nếu rỗng

                    // Luồng popup chỉ dùng khi người dùng muốn tìm đúng Số thứ tự
                    // Cho phép cả chuỗi số có 0 ở đầu (VD: 001, 012)
                    const isQueueNumber = /^[0-9]+$/.test(query);
                    if (!isQueueNumber) return; // fallback: vẫn gửi GET bình thường

                    evt.preventDefault();

                    // Reset modal state
                    contentEl.classList.add('d-none');
                    emptyEl.classList.add('d-none');
                    loadingEl.classList.remove('d-none');
                    contentEl.innerHTML = '';
                    results = [];
                    currentIndex = 0;
                    modal.show();

                    fetch(`{{ route('search.queue') }}` + `?search=` + encodeURIComponent(query), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            loadingEl.classList.add('d-none');

                            if (!data || data.found !== true) {
                                emptyEl.classList.remove('d-none');
                                return;
                            }
                            results = Array.isArray(data.items) ? data.items : [];
                            if (results.length === 0) {
                                emptyEl.classList.remove('d-none');
                                return;
                            }
                            currentIndex = 0;
                            render(currentIndex);
                        })
                        .catch(() => {
                            loadingEl.classList.add('d-none');
                            emptyEl.classList.remove('d-none');
                        });
                });
            })();
        </script>

</body>

</html>