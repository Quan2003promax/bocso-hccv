@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-6">
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-900">Tạo người dùng mới</h2>
      <a class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md" href="{{ route('users.index') }}">
        <i class="fa fa-angle-double-left mr-2"></i> Trở lại danh sách
      </a>
    </div>
    <div class="p-6">
      {!! Form::open(array('route' => 'users.store','method'=>'POST')) !!}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Tên <span class="text-red-500">*</span></label>
          {!! Form::text('name', null, ['placeholder' => 'Tên','class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500', 'required']) !!}
          <p class="text-red-600 text-sm mt-1">{{ $errors->first('name') }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
          {!! Form::text('email', null, ['placeholder' => 'Email','class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500', 'required']) !!}
          <p class="text-red-600 text-sm mt-1">{{ $errors->first('email') }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Mật khẩu <span class="text-red-500">*</span></label>
          {!! Form::password('password', ['placeholder' => 'Mật khẩu','class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500', 'required']) !!}
          <p class="text-red-600 text-sm mt-1">{{ $errors->first('password') }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Nhập lại mật khẩu <span class="text-red-500">*</span></label>
          {!! Form::password('confirm-password', ['placeholder' => 'Nhập lại mật khẩu','class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500', 'required']) !!}
          <p class="text-red-600 text-sm mt-1">{{ $errors->first('confirm-password') }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Phòng ban</label>
          {!! Form::select('department_id', ['' => 'Chọn phòng ban'] + $departments, null, ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500']) !!}
          <p class="text-red-600 text-sm mt-1">{{ $errors->first('department_id') }}</p>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Vai trò <span class="text-red-500">*</span></label>
          {!! Form::select('roles[]', $roles,[], ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500','multiple', 'required']) !!}
          <p class="text-red-600 text-sm mt-1">{{ $errors->first('roles') }}</p>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Quyền trực tiếp (tùy chọn)</label>
          <div class="mt-2 max-h-40 overflow-y-auto border border-gray-300 rounded-md p-3">
            @php
              $permissionGroups = collect($permissions)->groupBy(function($permission) {
                  return explode('-', $permission)[0] ?? 'other';
              });
            @endphp
            
            @foreach($permissionGroups as $groupName => $groupPermissions)
              <div class="mb-3">
                <h6 class="font-medium text-gray-700 mb-2 text-capitalize">{{ $groupName }}</h6>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                  @foreach($groupPermissions as $permissionId => $permissionName)
                    <label class="inline-flex items-center">
                      <input type="checkbox" name="permissions[]" value="{{ $permissionId }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                      <span class="ml-2 text-sm text-gray-700">{{ $permissionName }}</span>
                    </label>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
          <p class="text-red-600 text-sm mt-1">{{ $errors->first('permissions') }}</p>
          <p class="text-gray-500 text-sm mt-1">Quyền trực tiếp sẽ được gán ngoài quyền từ vai trò</p>
        </div>
      </div>
      <div class="mt-6 text-right">
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black rounded-md">
          <i class="fas fa-save mr-2"></i> Tạo người dùng
        </button>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
@endsection
