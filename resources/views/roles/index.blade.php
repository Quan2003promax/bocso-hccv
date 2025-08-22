@extends('layouts.app')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Quản lý vai trò</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
              <li class="breadcrumb-item active">Quản lý vai trò</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<div class="col-md-12">
  <div class="card">
    <div class="card-header">
        <h2 class="card-title">Quản lý vai trò</h2>
        <div class="card-tools">
            <a class="btn btn-success" href="{{ route('roles.create') }}"><i class="fas fa-plus-square"></i> Thêm vai trò</a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered">
            <tr class="bg-blue text-center">
                <th width="50px">STT</th>
                <th>Tên</th>
                <th width="150px">Thao tác</th>
            </tr>
            @foreach ($roles as $key => $role)
            <tr>
                <td class="text-center">{{ ++$key }}</td>
                <td class="text-center">{{ $role->name }}</td>
                <td class="text-center">

                    @can('role-edit')
                        <a class="btn btn-sm btn-primary" href="{{ route('roles.edit',$role->id) }}">Sửa</a>
                    @endcan
                    @can('role-delete')
                        {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                            {!! Form::submit('Xóa', ['class' => 'btn btn-sm btn-danger delete_confirm']) !!}
                        {!! Form::close() !!}
                    @endcan
                </td>
            </tr>
            @endforeach
        </table>

        {!! $roles->render() !!}
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
        <div class="float-left">
            <div class="dataTables_info">
                Hiện thị {{ $roles->firstItem() }} của {{ $roles->lastItem() }} trong {{ $roles->total() }}
            </div>
        </div>
        <div class="float-right">
            {{ $roles->links() }}
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
        Swal.fire({
            title: 'Are you sure you want to delete this record?',
            text: "If you delete this, it will be gone forever.",
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            showDenyButton: true,
            denyButtonText: 'Cancel',
        })
        .then((result) => {
            if (result.isConfirmed) {
                form.submit();
             } else if (result.isDenied) {
                Swal.fire('Your record is safe', '', 'info')
            }

        });
    });
});
</script>
@endsection
