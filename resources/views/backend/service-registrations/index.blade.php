@extends('layouts.app')

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
@endpush
