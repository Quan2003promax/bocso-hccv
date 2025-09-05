@extends('layouts.app')
<style>
    .fade-in {
        animation: fadeInRow 0.8s ease-out;
    }

    @keyframes fadeInRow {
        from {
            opacity: 0;
            transform: translateY(-8px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Filter styles */
    .filter-section {
        transition: all 0.3s ease;
    }

    .filter-section:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .filter-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-button {
        transition: all 0.2s ease;
    }

    .filter-button:hover {
        transform: translateY(-1px);
    }

    .active-filters {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Quản lý đăng ký dịch vụ
                </h3>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Filters Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 filter-section">
            <div class="p-6">
                <h4 class="text-md font-semibold text-gray-900 mb-4">
                    <i class="fas fa-filter me-2"></i>
                    Bộ lọc tìm kiếm
                </h4>
                <form method="GET" action="{{ route('admin.service-registrations.index') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                    <!-- Search Box -->
                    <div style="padding-right: 10px;">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                        <input type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Số thứ tự, họ tên, CCCD..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 filter-input">
                    </div>

                    <!-- Department Filter -->
                    <div style="padding-right: 10px;">
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Phòng ban</label>
                        <select id="department_id"
                            name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 filter-input">
                            <option value="">Tất cả phòng ban</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div style="padding-right: 10px;">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                        <select id="status"
                            name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 filter-input">
                            <option value="">Tất cả trạng thái</option>
                            @foreach($statuses as $key => $status)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex  space-x-2">
                        <button type="submit"
                            class="flex items-center justify-center px-4 py-2 bg-blue-600 text-black rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 filter-button">
                            <i class="fas fa-search mr-2"></i>
                            Lọc
                        </button>
                        <a href="{{ route('admin.service-registrations.index') }}"
                            class="flex items-center justify-center px-4 py-2 bg-gray-500 text-black rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 filter-button">
                            <i class="fas fa-times mr-2"></i>
                            Xóa lọc
                        </a>
                    </div>
                </form>

                <!-- Active Filters Display -->
                @if(request('search') || request('department_id') || request('status'))
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md active-filters">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-600 me-2"></i>
                        <span class="text-sm text-blue-800">
                            Đang lọc:
                            @if(request('search'))
                            <span class="font-medium">"{{ request('search') }}"</span>
                            @endif
                            @if(request('department_id'))
                            @php $selectedDept = $departments->find(request('department_id')); @endphp
                            @if($selectedDept)
                            <span class="font-medium">Phòng ban: {{ $selectedDept->name }}</span>
                            @endif
                            @endif
                            @if(request('status'))
                            <span class="font-medium">Trạng thái: {{ $statuses[request('status')] ?? request('status') }}</span>
                            @endif
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số thứ tự</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ và tên</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SĐT</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phòng ban</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CCCD/CMND</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tài liệu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian đăng ký</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($registrations as $registration)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $registration->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $registration->queue_number }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $registration->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="mailto:{{ $registration->email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $registration->email }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="tel:{{ $registration->phone }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $registration->phone }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $registration->department->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $registration->identity_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('admin.service-registrations.show', $registration->id) }}"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200">
                                        <i class="fas fa-eye me-1"></i>
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $registration->created_at->format('H:i d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @cannot('service-registration-update-status')
                                    <span class="
                                    inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @switch($registration->status)
                                        @case('pending') bg-gray-100 text-gray-800 @break
                                        @case('received') bg-blue-100 text-blue-800 @break
                                        @case('processing') bg-yellow-100 text-yellow-800 @break
                                        @case('completed') bg-green-100 text-green-800 @break
                                        @case('returned') bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch
    ">
                                        {{ __("statuses.$registration->status") }}
                                    </span>
                                    @else
                                    <select
                                        class="status-select text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        data-registration-id="{{ $registration->id }}">
                                        <option value="pending" {{ $registration->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                        <option value="received" {{ $registration->status == 'received' ? 'selected' : '' }}>Đã tiếp nhận</option>
                                        <option value="processing" {{ $registration->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="completed" {{ $registration->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                        <option value="returned" {{ $registration->status == 'returned' ? 'selected' : '' }}>Trả hồ sơ</option>
                                    </select>
                                    @endcannot

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @can('service-registration-edit')
                                    <a href="{{ route('admin.service-registrations.show', $registration) }}"
                                        class="text-blue-600 hover:text-blue-900 me-3">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('service-registration-delete')
                                    <form action="{{ route('admin.service-registrations.destroy', $registration) }}"
                                        method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2"></i>
                                        <p>Chưa có đăng ký nào</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($registrations->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $registrations->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.userDepartments = @json(optional(auth() -> user()) -> departments -> pluck('id') ?? []);
</script>
<script>
    window.userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name'));
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form khi thay đổi select
        const departmentSelect = document.getElementById('department_id');
        const statusSelect = document.getElementById('status');

        if (departmentSelect) {
            departmentSelect.addEventListener('change', function() {
                this.closest('form').submit();
            });
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                this.closest('form').submit();
            });
        }

        // Debounce search input
        const searchInput = document.getElementById('search');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.closest('form').submit();
                }, 500);
            });
        }

        // Xử lý confirm delete với SweetAlert2
        document.querySelectorAll('.delete-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Bạn có chắc chắn muốn xóa đăng ký này?',
                    text: "Hành động này không thể hoàn tác!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Có, xóa nó!',
                    cancelButtonText: 'Hủy bỏ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Xử lý cập nhật trạng thái
        document.querySelectorAll('.status-select').forEach(function(select) {
            select.addEventListener('change', function() {
                const registrationId = this.getAttribute('data-registration-id');
                const newStatus = this.value;
                const originalStatus = this.getAttribute('data-original-status') || this.value;

                // Hiển thị loading
                this.disabled = true;
                this.style.opacity = '0.6';

                // Gửi request cập nhật
                fetch(`/admin/service-registrations/${registrationId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Cập nhật thành công
                            Swal.fire({
                                title: 'Thành công!',
                                text: 'Trạng thái đã được cập nhật',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Cập nhật data-original-status
                            this.setAttribute('data-original-status', newStatus);
                        } else {
                            // Cập nhật thất bại
                            Swal.fire({
                                title: 'Lỗi!',
                                text: data.message || 'Không thể cập nhật trạng thái',
                                icon: 'error'
                            });

                            // Khôi phục giá trị cũ
                            this.value = originalStatus;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi khi cập nhật trạng thái',
                            icon: 'error'
                        });

                        // Khôi phục giá trị cũ
                        this.value = originalStatus;
                    })
                    .finally(() => {
                        // Khôi phục select
                        this.disabled = false;
                        this.style.opacity = '1';
                    });
            });

            // Lưu giá trị ban đầu
            select.setAttribute('data-original-status', select.value);
        });
    });
</script>



<script>
    // SocketManager sẽ được khởi tạo tự động
    // Chúng ta chỉ cần đợi nó sẵn sàng
</script>
<script>
    function truncate(str, maxLength = 20) {
        return (str && str.length > maxLength) ? str.substring(0, maxLength - 3) + '...' : (str || 'Không có');
    }

    function makeDashboardRow(item) {
        const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
        const hasPermission = window.userPermissions.includes('service-registration-update-status');
        return `
    <tr class="hover:bg-gray-50" data-id="${item.id}">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.id}</td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                ${item.queue_number}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.full_name}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <a href="mailto:${item.email}" class="text-blue-600 hover:text-blue-800">${item.email}</a>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <a href="tel:${item.phone}" class="text-blue-600 hover:text-blue-800">${item.phone}</a>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.department}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.identity_number || '-'}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            ${
                item.document_file
                ? `
                <div class="flex flex-col space-y-1">
                    <a href="/admin/documents/${item.id}/view" target="_blank"
                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200">
                        <i class="fas fa-eye me-1"></i>
                        Xem tài liệu
                    </a>
                    <a href="/storage/${item.document_file}" download="${item.document_original_name}"
                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200">
                        <i class="fas fa-download me-1"></i>
                        Tải về
                    </a>
                    <small class="text-gray-400">${truncate(item.document_original_name, 25)}</small>
                    <small class="text-gray-400">${item.formatted_file_size || ''}</small>
                </div>
                `
                : '<span class="text-gray-400">Không có</span>'
            }
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.at}</td>
        <td class="px-6 py-4 whitespace-nowrap">
                ${hasPermission ? `
                    <select class="status-select text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        data-registration-id="${item.id}">
                        <option value="pending"    ${item.new_status === 'pending' ? 'selected' : ''}>Chờ xử lý</option>
                        <option value="received"   ${item.new_status === 'received' ? 'selected' : ''}>Đã tiếp nhận</option>
                        <option value="processing" ${item.new_status === 'processing' ? 'selected' : ''}>Đang xử lý</option>
                        <option value="completed"  ${item.new_status === 'completed' ? 'selected' : ''}>Hoàn thành</option>
                        <option value="returned"   ${item.new_status === 'returned' ? 'selected' : ''}>Trả hồ sơ</option>
                    </select>
                ` : `
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                        ${translateStatus(item.new_status)}
                    </span>
                `}
            </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <a href="/admin/service-registrations/${item.id}" class="text-blue-600 hover:text-blue-900 me-3">
                <i class="fas fa-eye"></i>
            </a>
            <form action="/admin/service-registrations/${item.id}" method="POST" class="inline delete-form">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="text-red-600 hover:text-red-900 delete-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>`;
    }
    function translateStatus(status) {
        switch (status) {
            case 'pending': return 'Chờ xử lý';
            case 'received': return 'Đã tiếp nhận';
            case 'processing': return 'Đang xử lý';
            case 'completed': return 'Hoàn thành';
            case 'returned': return 'Trả hồ sơ';
            default: return 'Không xác định';
        }
    }

    function getStatusBadge(status) {
        switch (status) {
            case 'pending':
                return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Chờ xử lý</span>';
            case 'received':
                return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Đã tiếp nhận</span>';
            case 'processing':
                return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-200 text-blue-800">Đang xử lý</span>';
            case 'completed':
                return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Hoàn thành</span>';
            case 'returned':
                return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Trả hồ sơ</span>';
            default:
                return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Không xác định</span>';
        }
    }
    // Sử dụng SocketManager để lắng nghe events
    function setupSocketListeners() {
        if (!window.socketManager || !window.socketManager.isSocketAvailable()) {
            console.log('SocketManager chưa sẵn sàng, thử lại sau...');
            setTimeout(setupSocketListeners, 1000);
            return;
        }

        // Lắng nghe registration created
        window.socketManager.listen('laravel_database_registrations', '.registration.created', (e) => {
            console.log('Received registration.created event:', e);
            
            const userDepartments = window.userDepartments || [];

            if (!userDepartments.includes(Number(e.department_id))) {
                return;
            }

            const tbody = document.querySelector('tbody');
            if (!tbody) return;

            // nếu có row "Chưa có đăng ký nào" thì xoá
            const emptyRow = tbody.querySelector('td[colspan]');
            if (emptyRow) emptyRow.parentElement.remove();

            // render row mới + animate
            const rowHtml = makeDashboardRow(e);
            prependRow(tbody, rowHtml);
            attachStatusListeners();
            attachDeleteListeners();
            
            // Lưu vào state
            window.socketManager.updateRegistration(e);
        }, 'registration-created');

        // Lắng nghe status updated
        window.socketManager.listen('laravel_database_status', '.status.updated', (e) => {
            console.log('Received status.updated event:', e);
            
            const userDepartments = window.userDepartments || [];
            if (!userDepartments.includes(Number(e.department_id))) {
                return;
            }

            // Cập nhật row trong table
            const row = document.querySelector(`tr[data-id="${e.id}"]`);
            if (row) {
                const statusCell = row.querySelector('.status-cell');
                if (statusCell) {
                    statusCell.innerHTML = getStatusBadge(e.new_status);
                }
            }
            
            // Lưu vào state
            window.socketManager.updateRegistration(e);
        }, 'status-updated');

        // Lắng nghe registration deleted
        window.socketManager.listen('laravel_database_status', '.status.deleted', (e) => {
            console.log('Received status.deleted event:', e);
            
            const userDepartments = window.userDepartments || [];
            if (!userDepartments.includes(Number(e.department_id))) {
                return;
            }

            // Xóa row khỏi table
            const row = document.querySelector(`tr[data-id="${e.id}"]`);
            if (row) {
                row.classList.add('fade-out');
                setTimeout(() => row.remove(), 800);
            }
            
            // Xóa khỏi state
            window.socketManager.removeRegistration(e.id);
        }, 'status-deleted');

        console.log('Socket listeners setup completed');
    }

    // Khôi phục state từ SocketManager
    function restoreStateFromSocketManager() {
        if (!window.socketManager) return;

        const savedRegistrations = window.socketManager.getAllRegistrations();
        if (savedRegistrations.length === 0) return;

        const tbody = document.querySelector('tbody');
        if (!tbody) return;

        // Xóa empty row nếu có
        const emptyRow = tbody.querySelector('td[colspan]');
        if (emptyRow) emptyRow.parentElement.remove();

        // Thêm lại các registrations đã lưu
        savedRegistrations.forEach(registration => {
            const existingRow = tbody.querySelector(`tr[data-id="${registration.id}"]`);
            if (!existingRow) {
                const rowHtml = makeDashboardRow(registration);
                prependRow(tbody, rowHtml);
            }
        });

        // Reattach listeners
        attachStatusListeners();
        attachDeleteListeners();

        console.log(`Restored ${savedRegistrations.length} registrations from state`);
    }

    // Khởi tạo listeners khi DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        setupSocketListeners();
        
        // Khôi phục state sau một chút delay để đảm bảo SocketManager đã sẵn sàng
        setTimeout(restoreStateFromSocketManager, 500);
    });


    // ví dụ: rebind JS cho status select (tùy bạn đã viết)
    function attachStatusListeners() {
        document.querySelectorAll('.status-select').forEach(sel => {
            sel.onchange = function() {
                const id = this.dataset.registrationId;
                const newStatus = this.value;
                // gọi AJAX update status...
                fetch(`/admin/service-registrations/${id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Cập nhật thành công
                            Swal.fire({
                                title: 'Thành công!',
                                text: 'Trạng thái đã được cập nhật',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Cập nhật data-original-status
                            this.setAttribute('data-original-status', newStatus);
                        } else {
                            // Cập nhật thất bại
                            Swal.fire({
                                title: 'Lỗi!',
                                text: data.message || 'Không thể cập nhật trạng thái',
                                icon: 'error'
                            });

                            // Khôi phục giá trị cũ
                            this.value = originalStatus;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi khi cập nhật trạng thái',
                            icon: 'error'
                        });

                        // Khôi phục giá trị cũ
                        this.value = originalStatus;
                    })
                    .finally(() => {
                        // Khôi phục select
                        this.disabled = false;
                        this.style.opacity = '1';
                    });

            }
        });
    }

    function attachDeleteListeners() {
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Bạn có chắc chắn muốn xóa đăng ký này?',
                    text: "Hành động này không thể hoàn tác!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Có, xóa nó!',
                    cancelButtonText: 'Hủy bỏ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    }

    function prependRow(tbody, rowHtml) {
        const wrapper = document.createElement('tbody');
        wrapper.innerHTML = rowHtml;
        const newRow = wrapper.firstElementChild;

        // thêm hiệu ứng
        newRow.classList.add('fade-in');
        newRow.addEventListener('animationend', () => {
            newRow.classList.remove('fade-in'); // gỡ class để lần sau lại chạy
        }, {
            once: true
        });

        tbody.prepend(newRow);
    }
</script>
@endpush