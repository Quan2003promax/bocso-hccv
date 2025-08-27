@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-900">Quản lý người dùng</h2>
      <div>
        @can('user-create')
        <a class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black rounded-md" href="{{ route('users.create') }}">
          <i class="fas fa-plus-square mr-2"></i> Thêm người dùng
        </a>
        @endcan
      </div>
    </div>
    <div class="p-6">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($users as $key => $user)
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $users->firstItem() + $key }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  @if(!empty($user->getRoleNames()))
                    @foreach($user->getRoleNames() as $v)
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mr-1">{{ $v }}</span>
                    @endforeach
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  @can('user-edit')
                  <a class="inline-flex items-center px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-black rounded-md mr-2" href="{{ route('users.edit',$user->id) }}"><i class="fa fa-user" aria-hidden="true"></i></a>
                  @endcan
                  @can('user-delete')
                  {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-black rounded-md delete_confirm"><i class="fa fa-trash" aria-hidden="true"></i></button>
                  {!! Form::close() !!}
                  @endcan
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-600">Hiển thị {{ $users->firstItem() }} đến {{ $users->lastItem() }} của {{ $users->total() }}</div>
        <div>{{ $users->links() }}</div>
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
        title: 'Bạn thực sự muốn xóa người dùng này?',
        text: 'Người dùng này sẽ biến mất vĩnh viễn.',
        icon: 'warning',
        showDenyButton: true,
        confirmButtonText: 'Xác nhận',
        denyButtonText: 'Hủy bỏ'
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
