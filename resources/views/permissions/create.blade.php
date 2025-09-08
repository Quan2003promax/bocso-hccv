@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Tạo quyền mới</h2>
                    <p class="text-sm text-gray-600 mt-1">Thêm quyền mới vào hệ thống</p>
                </div>
                <div>
                    <a class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700  rounded-md transition duration-150 ease-in-out" href="{{ route('permissions.index') }}">
                        <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('permissions.store') }}">
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

                <!-- Permission Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên quyền <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           placeholder="Ví dụ: user-create, role-edit" 
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Tên quyền nên có định dạng: resource-action (ví dụ: user-create, role-edit)
                    </p>
                </div>

                <!-- Guard Name -->
                <div class="mb-6">
                    <label for="guard_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Guard Name
                    </label>
                    <input type="text" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('guard_name') border-red-300 @enderror" 
                           id="guard_name" 
                           name="guard_name" 
                           value="{{ old('guard_name', 'web') }}" 
                           placeholder="web">
                    @error('guard_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Để trống để sử dụng giá trị mặc định 'web'
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700  rounded-md transition duration-150 ease-in-out">
                        <i class="fas fa-save mr-2"></i> Tạo quyền
                    </button>
                    <a href="{{ route('permissions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700  rounded-md transition duration-150 ease-in-out">
                        <i class="fas fa-times mr-2"></i> Hủy
                    </a>
                </div>
                
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Quyền mới sẽ được tạo ngay lập tức
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
