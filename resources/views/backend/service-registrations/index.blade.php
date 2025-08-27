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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phòng ban</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $registration->department->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $registration->created_at->format('H:i d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select class="status-select text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        data-registration-id="{{ $registration->id }}">
                                        <option value="pending" {{ $registration->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                        <option value="received" {{ $registration->status == 'received' ? 'selected' : '' }}>Đã tiếp nhận</option>
                                        <option value="processing" {{ $registration->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="completed" {{ $registration->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                        <option value="returned" {{ $registration->status == 'returned' ? 'selected' : '' }}>Trả hồ sơ</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.service-registrations.show', $registration) }}"
                                        class="text-blue-600 hover:text-blue-900 me-3">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.service-registrations.destroy', $registration) }}"
                                        method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center">
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
                    {{ $registrations->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="http://localhost:6001/socket.io/socket.io.js"></script>
<script src="https://unpkg.com/laravel-echo@1.15.3/dist/echo.iife.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
<script>
    // echo = new EchoCtor({
    //     broadcaster: 'socket.io',
    //     host: `${location.hostname}:6001`,
    //     transports: ['websocket', 'polling'],
    // });

    function makeDashboardRow(item) {
        return `
    <tr class="hover:bg-gray-50" data-id="${item.id}">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.id}</td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">${item.queue_number}</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.full_name}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.department}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.created_at}</td>
        <td class="px-6 py-4 whitespace-nowrap">
            <select class="status-select text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                data-registration-id="${item.id}">
                <option value="pending"   ${item.status==='pending'?'selected':''}>Chờ xử lý</option>
                <option value="received"  ${item.status==='received'?'selected':''}>Đã tiếp nhận</option>
                <option value="processing"${item.status==='processing'?'selected':''}>Đang xử lý</option>
                <option value="completed" ${item.status==='completed'?'selected':''}>Hoàn thành</option>
                <option value="returned"  ${item.status==='returned'?'selected':''}>Trả hồ sơ</option>
            </select>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <a href="${item.show_url}" class="text-blue-600 hover:text-blue-900 me-3">
                <i class="fas fa-eye"></i>
            </a>
            <form action="${item.delete_url}" method="POST" class="inline delete-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="text-red-600 hover:text-red-900 delete-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>`;
    }

    echo.channel('laravel_database_registrations')
        .listen('.registration.created', (e) => {
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
        });

    // echo.channel('laravel_database_registrations')
    //     .listen('.registration.created', (e) => {
    //         console.log('📥 New registration:', e);

    //         const tbody = document.querySelector('tbody');
    //         if (!tbody) return;

    //         // nếu có dòng "Chưa có đăng ký nào" thì xoá
    //         const emptyRow = tbody.querySelector('td[colspan]');
    //         if (emptyRow) emptyRow.parentElement.remove();

    //         // thêm record mới vào đầu bảng
    //         const wrapper = document.createElement('tbody');
    //         wrapper.innerHTML = makeDashboardRow(e);
    //         tbody.prepend(wrapper.firstElementChild);
    //         prependRow(tbody, wrapper.innerHTML);
    //         // gắn lại event cho select và delete form nếu đang dùng JS xử lý
    //         attachStatusListeners();
    //         attachDeleteListeners();
    //     });

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