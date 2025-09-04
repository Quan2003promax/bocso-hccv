@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-6">
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-900">Chỉnh sửa người dùng: {{ $user->name }}</h2>
      <a class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md" href="{{ route('users.index') }}">
        <i class="fa fa-angle-double-left mr-2"></i> Trở lại danh sách
      </a>
    </div>
    <div class="p-6">
      {!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id]]) !!}
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
          <label class="block text-sm font-medium text-gray-700">Mật khẩu (để trống nếu không thay đổi)</label>
          {!! Form::password('password', ['placeholder' => 'Mật khẩu mới','class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500']) !!}
          <p class="text-red-600 text-sm mt-1">{{ $errors->first('password') }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Nhập lại mật khẩu</label>
          {!! Form::password('confirm-password', ['placeholder' => 'Nhập lại mật khẩu mới','class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500']) !!}
          <p class="text-red-600 text-sm mt-1">{{ $errors->first('confirm-password') }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Phòng ban</label>

          {{-- Nút chọn tất cả --}}
          <div class="mb-2">
            <label class="flex items-center space-x-2 cursor-pointer">
              <input type="checkbox" id="checkAllDepartments" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
              <span class="font-semibold text-sm text-blue-600"> Chọn tất cả</span>
            </label>
          </div>

          <div class="mt-2 space-y-2" id="departmentCheckboxes">
            @foreach($departments as $id => $name)
            <label class="flex items-center space-x-2">
              {!! Form::checkbox(
              'department_ids[]',
              $id,
              in_array(
              $id,
              old(
              'department_ids',
              isset($user) ? $user->departments->pluck('id')->toArray() : []
              )
              ),
              ['class' => 'department-item rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500']
              ) !!}
              <span>{{ $name }}</span>
            </label>
            @endforeach
          </div>

          <p class="text-red-600 text-sm mt-1">{{ $errors->first('department_ids') }}</p>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Vai trò <span class="text-red-500">*</span></label>
          {!! Form::select('roles[]', $roles, $userRole, ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500','multiple', 'required']) !!}
          <p class="text-red-600 text-sm mt-1">{{ $errors->first('roles') }}</p>
        </div>
      </div>
      <div class="mt-6 text-right">
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
          <i class="fas fa-save mr-2"></i> Cập nhật người dùng
        </button>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
@endsection
