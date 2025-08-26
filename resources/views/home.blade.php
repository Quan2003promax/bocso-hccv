<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống bốc số thứ tự - Dịch vụ hành chính công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }
        .form-section {
            background: #f8f9fa;
            padding: 60px 0;
        }
        .queue-section {
            padding: 60px 0;
        }
        .queue-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .queue-card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .nav-link {
            font-weight: 500;
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
                                <li><hr class="dropdown-divider"></li>
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
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">
                <i class="fas fa-clipboard-list me-3"></i>
                Hệ thống bốc số thứ tự
            </h1>
            <p class="lead mb-0">Dịch vụ hành chính công - Phục vụ nhân dân 24/7</p>
        </div>
    </section>

    <!-- Form Section -->
    <section class="form-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card queue-card">
                        <div class="card-header bg-primary text-white text-center py-4">
                            <h3 class="mb-0">
                                <i class="fas fa-user-plus me-2"></i>
                                Đăng ký dịch vụ hành chính
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Vui lòng sửa các lỗi sau:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form action="{{ route('service.register') }}" method="POST">
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
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Đăng ký dịch vụ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">STT</th>
                                            <th>Số thứ tự</th>
                                            <th>Họ và tên</th>
                                            <th>Phòng ban</th>
                                            <th>Thời gian đăng ký</th>
                                            <th class="text-center">Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody id="queue-tbody">
                                        @forelse($pendingRegistrations as $index => $registration)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-primary fs-6">{{ $registration->queue_number }}</span>
                                                </td>
                                                <td>{{ $registration->full_name }}</td>
                                                <td>{{ $registration->department->name }}</td>
                                                <td>{{ $registration->created_at->format('H:i d/m/Y') }}</td>
                                                <td class="text-center">
                                                    @switch($registration->status)
                                                        @case('pending')
                                                            <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                                            @break
                                                        @case('received')
                                                            <span class="badge bg-info text-dark">Đã tiếp nhận</span>
                                                            @break
                                                        @case('processing')
                                                            <span class="badge bg-primary text-white">Đang xử lý</span>
                                                            @break
                                                        @case('completed')
                                                            <span class="badge bg-success text-white">Hoàn thành</span>
                                                            @break
                                                        @case('returned')   
                                                            <span class="badge bg-secondary text-white">Trả hồ sơ</span>
                                                            @break
                                                        @default
                                                    @endswitch
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-3"></i>
                                                    <br>Chưa có đăng ký nào
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
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
    transports: ['websocket','polling'],
  });

  // Test bắt event từ /test-status
  echo.channel('laravel_database_status')
      .listen('.status.updated', (e) => {
        console.log('🔥 received .status.updated', e);
        // alert(`Status: ${e.status}\nMessage: ${e.message}`);
      });

  echo.connector.socket.on('connect',      () => console.log('✅ socket connected'));
  echo.connector.socket.on('connect_error',(err) => console.error('❌ connect_error', err));
</script>

</body>
</html>
