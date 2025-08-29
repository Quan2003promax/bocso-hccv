@extends('layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Quyền</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
              <li class="breadcrumb-item active">Quyền</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Danh sách quyền</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-success btn-create" data-toggle="modal" data-target="#PermissionModal">
                <i class="fas fa-plus-square"></i> Thêm quyền
                </button>
            </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr class="bg-blue">
                    <th width="30px"></th>
                    <th>Tên</th>
                    <th width="150px">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                </tfoot>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Modal UpadateOrCreate Permission -->

    <div class="modal fade" id="PermissionModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form method="POST" action="" id="permissionForm">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Thêm quyền</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_method" id="permission_method" value="POST">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Tên quyền</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Điền tên" value="" required>
                            @error('name')
                                <p class="mt-2 mb-0 error text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" id="savedata">Lưu</button>
                </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->

    </section>
    <!-- /.content -->
<script src="{{ asset('backend/plugins/jquery/jquery.min.js') }}"></script>
<script>
  $(function () {
    var table = $("#example1").DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "language": {
                "url": "{{ asset('backend/json/jsondatatable.json') }}"
       },
       "ajax": {
           url: "{{ route('permissions.index') }}",
           dataSrc: 'permissions'
       },
       'columns':[
                {
                    className:      'dt-control',
                    orderable:      false,
                    data:           null,
                    defaultContent: '<i class="nav-icon far fa-circle text-info"></i>'
                },
                { data: 'name' },
                { data: 'id',
                    orderable: false,
                    render: function(data){
                        return '<button class="btn btn-sm btn-info btn-edit mr-1" data-id="'+data+'">Sửa</button>'+
                                '<button class="btn btn-sm btn-danger btn-delete" data-id="'+data+'">Xóa</button>';
                    }
                }
        ],
       order: [[1, 'desc']],
        "columnDefs": [
                { 'className': 'dt-center','targets': '_all' }
       ]
    });
    //Plus detail
    $('#example1 tbody').on('click', 'td.dt-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });
    function format ( rowData ) {
            return '<table class="table table-bordered">'+
                '<tr style="background: #f9f9f9">'+
                    '<th width="30%">Title</th>'+
                    '<th width="70%">Details</th>'+
                '</tr>'+
                '<tr>'+
                    '<td>ID:</td>'+
                    '<td>'+rowData.id+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<td>Name:</td>'+
                    '<td>'+rowData.name+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<td>Created at:</td>'+
                    '<td>'+new Date(rowData.created_at).toLocaleString()+'</td>'+
                '</tr>'+
            '</table>';
    };
    //create
        $('.btn-create').on('click', function (e) {
            e.preventDefault;
            var url = '{{ route("permissions.store") }}';
            $('.modal-title').html("Thêm quyền");
            $('#permissionForm').attr('action', url);
            $('#permission_method').attr('value', 'POST');
            $('#id').val('');
            $('#name').val('');
        });
        //edit
        $('#example1').on('click', '.btn-edit', function () {
            var permission_id = $(this).data('id');
            var url = '{{ route("permissions.update","") }}' +'/'+ permission_id;
            var rows = table.rows().data().toArray();
            var found = rows.find(function(r){ return String(r.id) === String(permission_id); });
            if(found){
                $('#PermissionModal').modal('show');
                $('.modal-title').html("Sửa quyền");
                $('#id').val(permission_id);
                $('#name').val(found.name);
                $('#permissionForm').attr('action', url);
                $('#permission_method').attr('value', 'PATCH');
            } else {
                $.get('{{ route("permissions.index") }}', function(data){
                    var item = (data.permissions || []).find(function(v){ return String(v.id) === String(permission_id); });
                    if(item){
                        $('#PermissionModal').modal('show');
                        $('.modal-title').html("Sửa quyền");
                        $('#id').val(permission_id);
                        $('#name').val(item.name);
                        $('#permissionForm').attr('action', url);
                        $('#permission_method').attr('value', 'PATCH');
                    }
                });
            }
        });


        $('#PermissionModal').on('hidden.bs.modal', function () {
            $(this).find('form').trigger('reset');
            $('.error').html('');
            $('#name').removeClass("is-invalid");
        });

        //Delete
        $('#example1').on("click", ".btn-delete", function() {
            var permission_id = $(this).data('id');
            var url = '{{ route("permissions.destroy","") }}' +'/'+ permission_id;

            Swal.fire({
                title: 'Bạn chắc chắn muốn xóa dữ liệu này!',
                text: "Nếu bạn xóa dữ liệu này sẽ biến mất vĩnh viễn.",
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xác nhận xóa',
                showCancelButton: true,
                cancelButtonText: "Hủy",

            }).then((result) => {
                if (result.value == true) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        cache: false,
                        data: {
                            _token:'{{ csrf_token() }}',
                        },
                        success: function (response){
                            Swal.fire(
                                "Đã xóa!",
                                "Dữ liệu đã được xóa vĩnh viễn.",
                                "success"
                                ).then(function(){
                                    table.ajax.reload(null, false);
                                });
                        },
                    });
                }else{
                    Swal.fire('Dữ liệu được bảo toàn', '', 'info')
                }

            });
        });

        @if(count($errors))
            $('#PermissionModal').modal('show');
        @endif
  });
</script>
@endsection


