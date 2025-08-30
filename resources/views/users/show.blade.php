@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-900">Chi tiết người dùng</h2>
      <div>
        @can('user-edit')
        <a class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-black rounded-md mr-2" href="{{ route('users.edit', $user->id) }}">
          <i class="fas fa-edit mr-2"></i> Sửa người dùng
        </a>
        @endcan
        <a class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-black rounded-md" href="{{ route('users.index') }}">
          <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
      </div>
    </div>
    
    <div class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Thông tin cơ bản -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin cơ bản</h3>
          <div class="space-y-3">
            <div>
              <label class="block text-sm font-medium text-gray-700">ID:</label>
              <p class="mt-1 text-sm text-gray-900">{{ $user->id }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Tên:</label>
              <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email:</label>
              <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Ngày tạo:</label>
              <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Ngày cập nhật:</label>
              <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i:s') }}</p>
            </div>
          </div>
        </div>

        <!-- Phòng ban -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Phòng ban</h3>
          @if($user->department)
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700">Tên phòng ban:</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->department->name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Mô tả:</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->department->description ?? 'Không có mô tả' }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Trạng thái:</label>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->department->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                  {{ $user->department->status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                </span>
              </div>
            </div>
          @else
            <p class="text-gray-500">Chưa được phân công phòng ban</p>
          @endif
        </div>

        <!-- Vai trò -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Vai trò</h3>
          @if(!empty($user->getRoleNames()))
            <div class="space-y-2">
              @foreach($user->getRoleNames() as $role)
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                  {{ $role }}
                </span>
              @endforeach
            </div>
          @else
            <p class="text-gray-500">Chưa được gán vai trò</p>
          @endif
        </div>

        <!-- Quyền trực tiếp -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Quyền trực tiếp</h3>
          @if($user->permissions->count() > 0)
            <div class="space-y-2">
              @foreach($user->permissions as $permission)
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                  {{ $permission->name }}
                </span>
              @endforeach
            </div>
          @else
            <p class="text-gray-500">Chỉ có quyền từ vai trò</p>
          @endif
        </div>
      </div>

      <!-- Tổng hợp quyền -->
      <div class="mt-6 bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Tổng hợp quyền (từ vai trò + trực tiếp)</h3>
        @php
          $allPermissions = $user->getAllPermissions();
        @endphp
        @if($allPermissions->count() > 0)
          <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            @foreach($allPermissions as $permission)
              <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                {{ $permission->name }}
              </span>
            @endforeach
          </div>
        @else
          <p class="text-gray-500">Không có quyền nào</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
