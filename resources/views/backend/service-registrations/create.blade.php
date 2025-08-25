<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-user-plus me-2"></i>
            {{ __('Đăng ký dịch vụ mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Vui lòng sửa các lỗi sau:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.service-registrations.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user me-2"></i>Họ và tên <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('full_name') border-red-500 @enderror" 
                                       id="full_name" 
                                       name="full_name" 
                                       value="{{ old('full_name') }}" 
                                       required>
                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="birth_year" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar me-2"></i>Năm sinh <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('birth_year') border-red-500 @enderror" 
                                       id="birth_year" 
                                       name="birth_year" 
                                       value="{{ old('birth_year') }}" 
                                       min="1900" 
                                       max="{{ date('Y') + 1 }}" 
                                       required>
                                @error('birth_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="identity_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-id-card me-2"></i>Số căn cước công dân <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('identity_number') border-red-500 @enderror" 
                                       id="identity_number" 
                                       name="identity_number" 
                                       value="{{ old('identity_number') }}" 
                                       required>
                                @error('identity_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-building me-2"></i>Phòng ban <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('department_id') border-red-500 @enderror" 
                                        id="department_id" 
                                        name="department_id" 
                                        required>
                                    <option value="">Chọn phòng ban</option>
                                    @foreach(\App\Models\Department::where('status', 'active')->get() as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between items-center">
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-arrow-left me-2"></i>
                                Quay lại Dashboard
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition duration-200">
                                <i class="fas fa-paper-plane me-2"></i>
                                Đăng ký dịch vụ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
