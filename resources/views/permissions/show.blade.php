@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Chi tiết quyền</h2>
                    <p class="text-sm text-gray-600 mt-1">Thông tin chi tiết về quyền: {{ $permission->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    @can('permission-edit')
                        <a href="{{ route('permissions.edit', $permission->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700  rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-edit mr-2"></i> Sửa
                        </a>
                    @endcan
                    <a href="{{ route('permissions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700  rounded-md transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-2"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Permission Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Thông tin quyền
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-500">ID:</span>
                            <span class="text-sm text-gray-900 font-mono">{{ $permission->id }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-500">Tên quyền:</span>
                            <span class="text-sm text-gray-900 font-semibold">{{ $permission->name }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-500">Guard Name:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $permission->guard_name }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-500">Ngày tạo:</span>
                            <span class="text-sm text-gray-900">{{ $permission->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm font-medium text-gray-500">Ngày cập nhật:</span>
                            <span class="text-sm text-gray-900">{{ $permission->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Roles using this permission -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-users mr-2 text-green-500"></i>
                        Vai trò sử dụng quyền này
                    </h3>
                    
                    @if($permission->roles->count() > 0)
                        <div class="space-y-3">
                            @foreach($permission->roles as $role)
                                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                    <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-semibold text-gray-700">
                                                {{ $role->name }}
                                            </h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                ID: {{ $role->id }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Guard:</span>
                                            <span class="text-sm text-gray-900 font-medium">{{ $role->guard_name }}</span>
                                        </div>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-sm text-gray-600">Ngày tạo:</span>
                                            <span class="text-sm text-gray-900">{{ $role->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    <span class="text-sm text-blue-700">
                                        Tổng cộng: <strong>{{ $permission->roles->count() }}</strong> vai trò đang sử dụng quyền này
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-yellow-800">Chưa có vai trò</h4>
                                    <p class="text-sm text-yellow-700 mt-1">Quyền này chưa được gán cho vai trò nào.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-clock mr-1"></i>
                    Cập nhật lần cuối: {{ $permission->updated_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
