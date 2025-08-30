@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Quản lý vai trò</h2>
                <p class="text-sm text-gray-600 mt-1">Quản lý các vai trò và quyền hạn trong hệ thống</p>
            </div>
            <div>
                @can('role-create')
                    <a class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black rounded-md transition duration-150 ease-in-out" href="{{ route('roles.create') }}">
                        <i class="fas fa-plus-square mr-2"></i> Thêm vai trò
                    </a>
                @endcan
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên vai trò</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quyền</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($roles as $key => $role)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                {{ ++$key }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                            <i class="fas fa-user-shield text-white text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                                        @if($role->name == 'Super-Admin')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-crown mr-1"></i>Quản trị viên cao cấp
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($role->permissions->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($role->permissions->take(3) as $permission)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-key mr-1"></i>{{ $permission->name }}
                                            </span>
                                        @endforeach
                                        @if($role->permissions->count() > 3)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                +{{ $role->permissions->count() - 3 }} quyền khác
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">Chưa có quyền</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-2">
                                    @can('role-edit')
                                        <a class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-black  rounded-md transition duration-150 ease-in-out" href="{{ route('roles.edit', $role->id) }}" title="Sửa" style="margin-right: 10px;">
                                            <i class="fas fa-edit mr-1"></i> Sửa
                                        </a>
                                    @endcan
                                    
                                    <a class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-black  rounded-md transition duration-150 ease-in-out" href="{{ route('roles.show', $role->id) }}" title="Xem chi tiết" style="margin-right: 10px;">
                                        <i class="fas fa-eye mr-1"></i> Chi tiết
                                    </a>
                                    
                                    @can('role-delete')
                                        @if($role->name != 'Super-Admin')
                                            {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700  text-black rounded-md transition duration-150 ease-in-out delete_confirm" title="Xóa">
                                                    <i class="fas fa-trash mr-1"></i> Xóa
                                                </button>
                                            {!! Form::close() !!}
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Hiển thị {{ $roles->firstItem() }} đến {{ $roles->lastItem() }} trong {{ $roles->total() }} vai trò
                </div>
                <div class="flex items-center space-x-2">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete_confirm').forEach(function(btn){
        btn.addEventListener('click', function(event){
            const form = this.closest('form');
            event.preventDefault();
            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa vai trò này?',
                text: "Hành động này không thể hoàn tác!",
                icon: 'warning',
                showDenyButton: true,
                confirmButtonText: 'Có, xóa nó!',
                denyButtonText: 'Hủy bỏ',
                confirmButtonColor: '#dc2626',
                denyButtonColor: '#6b7280',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                } else if (result.isDenied) {
                    Swal.fire('Dữ liệu được bảo toàn', '', 'info');
                }
            });
        });
    });
});
</script>
@endsection
