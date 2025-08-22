@extends('layouts.app')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Quản lý người dùng</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
              <li class="breadcrumb-item active">Quản lý người dùng</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

<div class="col-md-12">
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Quản lý người dùng</h2>
      <div class="card-tools">
        @can('user-create')
        <a class="btn btn-success" href="{{ route('users.create') }}"><i class="fas fa-plus-square"></i> Thêm người dùng</a>
        @endcan
      </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-responsive">
      <table class="table table-striped table-bordered">
      <tr class="bg-blue text-center">
        <th width="50px">STT.</th>
        <th>Tên</th>
        <th>Email</th>
        <th>Vai trò</th>
        <th width="150px">Thao tác</th>
      </tr>
      @foreach ($users as $key => $user)
        <tr>
          <td class="text-center">{{ ++$key }}</td>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td class="text-center">
            @if(!empty($user->getRoleNames()))
              @foreach($user->getRoleNames() as $v)
                <label class="badge badge-success">{{ $v }}</label>
              @endforeach
            @endif
          </td>
          <td class="text-center">
            @can('user-edit')
            <a class="btn btn-sm btn-primary" href="{{ route('users.edit',$user->id) }}">Sửa</a>
            @endcan
            @can('user-delete')
                {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                    {!! Form::submit('Xóa', ['class' => 'btn btn-sm btn-danger delete_confirm' ]) !!}
                {!! Form::close() !!}
            @endcan
          </td>
        </tr>
      @endforeach
      </table>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
      <div class="float-left">
          <div class="dataTables_info">
              Hiện thị {{ $users->firstItem() }} đến {{ $users->lastItem() }} của {{ $users->total() }}
          </div>
      </div>
      <div class="float-right">
          {{ $users->links() }}
      </div>
    </div>
  </div>
  <!-- /.card -->
</div>
@endsection
@section('scripts')
<script type="text/javascript">
$(function () {
    $('.delete_confirm').click(function(event) {
        var form =  $(this).closest("form");
        event.preventDefault();
        swal.fire({
            title: 'Bạn thực sự muốn xóa người dùng này?',
            text: "Người dùng này sẽ biến mất vĩnh viễn.",
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xác nhận',
            showDenyButton: true,
            denyButtonText: 'Hủy bỏ',
        })
        .then((result) => {
            if (result.isConfirmed) {
                form.submit();
             } else if (result.isDenied) {
                Swal.fire('Dữ liệu được bảo toàn', '', 'info')
            }

        });
    });
});
</script>
@endsection
