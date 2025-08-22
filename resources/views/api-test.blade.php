<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test API - Hệ thống bốc số thứ tự</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .api-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .response-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-top: 15px;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">
            <i class="fas fa-code me-2"></i>
            Test API - Hệ thống bốc số thứ tự
        </h1>

        <!-- Test đăng ký dịch vụ -->
        <div class="api-section">
            <h3><i class="fas fa-user-plus me-2"></i>Đăng ký dịch vụ</h3>
            <form id="registerForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="Nguyễn Văn Test" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="birth_year" class="form-label">Năm sinh</label>
                            <input type="number" class="form-control" id="birth_year" name="birth_year" value="1990" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="identity_number" class="form-label">Số căn cước</label>
                            <input type="text" class="form-control" id="identity_number" name="identity_number" value="123456789" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="department_id" class="form-label">Phòng ban</label>
                            <select class="form-select" id="department_id" name="department_id" required>
                                <option value="">Chọn phòng ban</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Đăng ký qua API
                </button>
            </form>
            <div id="registerResponse" class="response-box" style="display: none;"></div>
        </div>

        <!-- Test lấy danh sách phòng ban -->
        <div class="api-section">
            <h3><i class="fas fa-building me-2"></i>Lấy danh sách phòng ban</h3>
            <button class="btn btn-info" onclick="getDepartments()">
                <i class="fas fa-download me-2"></i>Lấy danh sách phòng ban
            </button>
            <div id="departmentsResponse" class="response-box" style="display: none;"></div>
        </div>

        <!-- Test lấy trạng thái hàng đợi -->
        <div class="api-section">
            <h3><i class="fas fa-list-ol me-2"></i>Lấy trạng thái hàng đợi</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="queue_department_id" class="form-label">Phòng ban (tùy chọn)</label>
                        <select class="form-select" id="queue_department_id" name="queue_department_id">
                            <option value="">Tất cả phòng ban</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-success mt-4" onclick="getQueueStatus()">
                        <i class="fas fa-clock me-2"></i>Lấy trạng thái hàng đợi
                    </button>
                </div>
            </div>
            <div id="queueStatusResponse" class="response-box" style="display: none;"></div>
        </div>

        <!-- Test kiểm tra số thứ tự -->
        <div class="api-section">
            <h3><i class="fas fa-search me-2"></i>Kiểm tra số thứ tự</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="check_queue_number" class="form-label">Số thứ tự</label>
                        <input type="text" class="form-control" id="check_queue_number" name="check_queue_number" placeholder="Nhập số thứ tự cần kiểm tra">
                    </div>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-warning mt-4" onclick="checkQueueNumber()">
                        <i class="fas fa-search me-2"></i>Kiểm tra
                    </button>
                </div>
            </div>
            <div id="checkQueueResponse" class="response-box" style="display: none;"></div>
        </div>

        <!-- Test lấy thống kê -->
        <div class="api-section">
            <h3><i class="fas fa-chart-bar me-2"></i>Lấy thống kê tổng quan</h3>
            <button class="btn btn-secondary" onclick="getStatistics()">
                <i class="fas fa-chart-line me-2"></i>Lấy thống kê
            </button>
            <div id="statisticsResponse" class="response-box" style="display: none;"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Lấy danh sách phòng ban khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            getDepartments();
        });

        // Đăng ký dịch vụ
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            fetch('/api/v1/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                const responseBox = document.getElementById('registerResponse');
                responseBox.style.display = 'block';
                responseBox.textContent = JSON.stringify(data, null, 2);
                responseBox.className = 'response-box ' + (data.success ? 'success' : 'error');
                
                if (data.success) {
                    // Cập nhật số thứ tự cần kiểm tra
                    document.getElementById('check_queue_number').value = data.data.queue_number;
                }
            })
            .catch(error => {
                const responseBox = document.getElementById('registerResponse');
                responseBox.style.display = 'block';
                responseBox.textContent = 'Lỗi: ' + error.message;
                responseBox.className = 'response-box error';
            });
        });

        // Lấy danh sách phòng ban
        function getDepartments() {
            fetch('/api/v1/departments')
            .then(response => response.json())
            .then(data => {
                const responseBox = document.getElementById('departmentsResponse');
                responseBox.style.display = 'block';
                responseBox.textContent = JSON.stringify(data, null, 2);
                responseBox.className = 'response-box ' + (data.success ? 'success' : 'error');
                
                if (data.success) {
                    // Cập nhật dropdown phòng ban
                    updateDepartmentDropdowns(data.data);
                }
            })
            .catch(error => {
                const responseBox = document.getElementById('departmentsResponse');
                responseBox.style.display = 'block';
                responseBox.textContent = 'Lỗi: ' + error.message;
                responseBox.className = 'response-box error';
            });
        }

        // Lấy trạng thái hàng đợi
        function getQueueStatus() {
            const departmentId = document.getElementById('queue_department_id').value;
            let url = '/api/v1/queue-status';
            if (departmentId) {
                url += '?department_id=' + departmentId;
            }
            
            fetch(url)
            .then(response => response.json())
            .then(data => {
                const responseBox = document.getElementById('queueStatusResponse');
                responseBox.style.display = 'block';
                responseBox.textContent = JSON.stringify(data, null, 2);
                responseBox.className = 'response-box ' + (data.success ? 'success' : 'error');
            })
            .catch(error => {
                const responseBox = document.getElementById('queueStatusResponse');
                responseBox.style.display = 'block';
                responseBox.textContent = 'Lỗi: ' + error.message;
                responseBox.className = 'response-box error';
            });
        }

        // Kiểm tra số thứ tự
        function checkQueueNumber() {
            const queueNumber = document.getElementById('check_queue_number').value;
            if (!queueNumber) {
                alert('Vui lòng nhập số thứ tự cần kiểm tra');
                return;
            }
            
            fetch('/api/v1/check-queue', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ queue_number: queueNumber })
            })
            .then(response => response.json())
            .then(data => {
                const responseBox = document.getElementById('checkQueueResponse');
                responseBox.style.display = 'block';
                responseBox.textContent = JSON.stringify(data, null, 2);
                responseBox.className = 'response-box ' + (data.success ? 'success' : 'error');
            })
            .catch(error => {
                const responseBox = document.getElementById('checkQueueResponse');
                responseBox.style.display = 'block';
                responseBox.textContent = 'Lỗi: ' + error.message;
                responseBox.className = 'response-box error';
            });
        }

        // Lấy thống kê
        function getStatistics() {
            fetch('/api/v1/statistics')
            .then(response => response.json())
            .then(data => {
                const responseBox = document.getElementById('statisticsResponse');
                responseBox.style.display = 'block';
                responseBox.textContent = JSON.stringify(data, null, 2);
                responseBox.className = 'response-box ' + (data.success ? 'success' : 'error');
            })
            .catch(error => {
                const responseBox = document.getElementById('statisticsResponse');
                responseBox.style.display = 'block';
                responseBox.textContent = 'Lỗi: ' + error.message;
                responseBox.className = 'response-box error';
            });
        }

        // Cập nhật dropdown phòng ban
        function updateDepartmentDropdowns(departments) {
            const dropdowns = ['department_id', 'queue_department_id'];
            
            dropdowns.forEach(dropdownId => {
                const dropdown = document.getElementById(dropdownId);
                if (dropdown) {
                    // Giữ lại option đầu tiên
                    const firstOption = dropdown.options[0];
                    dropdown.innerHTML = '';
                    dropdown.appendChild(firstOption);
                    
                    // Thêm các phòng ban
                    departments.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept.id;
                        option.textContent = dept.name;
                        dropdown.appendChild(option);
                    });
                }
            });
        }
    </script>
</body>
</html>
