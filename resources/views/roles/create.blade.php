@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Tạo vai trò mới</h2>
                    <p class="text-sm text-gray-600 mt-1">Thêm vai trò mới vào hệ thống</p>
                </div>
                <div>
                    <a class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition duration-150 ease-in-out" href="{{ route('roles.index') }}">
                        <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            
            <div class="p-6">
                @if (count($errors) > 0)
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Lỗi!</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Role Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên vai trò <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           placeholder="Ví dụ: Admin, Manager" 
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="my-8 border-gray-200">
                
                <!-- Permissions -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Quyền <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php
                            $permissionGroups = $permissions->groupBy(function($permission) {
                                return explode('-', $permission->name)[0] ?? 'other';
                            });
                        @endphp
                        
                        @foreach($permissionGroups as $groupName => $groupPermissions)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 rounded-t-lg">
                                    <h6 class="text-sm font-semibold text-gray-700 capitalize flex items-center">
                                        <i class="fas fa-layer-group mr-2 text-blue-500"></i>
                                        {{ $groupName }}
                                    </h6>
                                </div>
                                <div class="p-4 space-y-3">
                                    @foreach($groupPermissions as $permission)
                                        <div class="flex items-center">
                                            <input class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                                   type="checkbox" 
                                                   name="permission[]" 
                                                   value="{{ $permission->id }}" 
                                                   id="permission_{{ $permission->id }}">
                                            <label class="ml-3 text-sm text-gray-700 cursor-pointer hover:text-gray-900 transition-colors duration-150" 
                                                   for="permission_{{ $permission->id }}">
                                                <span class="font-medium">{{ $permission->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @error('permission')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black rounded-md transition duration-150 ease-in-out">
                        <i class="fas fa-save mr-2"></i> Tạo vai trò
                    </button>
                    <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-black rounded-md transition duration-150 ease-in-out">
                        <i class="fas fa-times mr-2"></i> Hủy
                    </a>
                </div>
                
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Vai trò mới sẽ được tạo ngay lập tức
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add select all functionality for each permission group
    const permissionGroups = document.querySelectorAll('.bg-white.border.border-gray-200.rounded-lg');
    
    permissionGroups.forEach(group => {
        const checkboxes = group.querySelectorAll('input[type="checkbox"]');
        const header = group.querySelector('h6');
        
        // Add select all checkbox to header
        const selectAllCheckbox = document.createElement('input');
        selectAllCheckbox.type = 'checkbox';
        selectAllCheckbox.className = 'h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mr-2';
        selectAllCheckbox.id = 'select_all_' + Math.random().toString(36).substr(2, 9);
        
        header.appendChild(selectAllCheckbox);
        
        // Handle select all functionality
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Update select all when individual checkboxes change
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                const someChecked = Array.from(checkboxes).some(cb => cb.checked);
                
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            });
        });
    });
});
</script>
@endsection
