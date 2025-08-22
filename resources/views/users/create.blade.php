@extends('layouts.app')
@section('content')
<!-- general form elements -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Tạo mới người dùng</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
              <li class="breadcrumb-item active">Tạo mới người dùng</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

<div class="col-md-12">
    <div class="card card-default">
        <div class="card-header">
        <h2 class="card-title">Tạo người dùng mới</h2>
            <div class="card-tools">
                <a class="btn btn-success" href="{{ route('users.index') }}"><i class="fa fa-angle-double-left"></i> Trở lại danh sách người dùng</a>
            </div>
        </div>

        <!-- /.card-header -->
        <!-- form start -->
        {!! Form::open(array('route' => 'users.store','method'=>'POST')) !!}
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            {!! Form::text('name', null, array('value' => '{{ old("name") }}', 'placeholder' => 'Tên','class' => 'form-control')) !!}
                            <span class="text-danger">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Email:</strong>
                            {!! Form::text('email', null, array('value' => '{{ old("email") }}', 'placeholder' => 'Email','class' => 'form-control')) !!}
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Mật khẩu:</strong>
                            {!! Form::password('password', array('placeholder' => 'Mật khẩu','class' => 'form-control')) !!}
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Nhập lại mật khẩu:</strong>
                            {!! Form::password('confirm-password', array('placeholder' => 'Nhập lại mật khẩu','class' => 'form-control')) !!}
                            <span class="text-danger">{{ $errors->first('confirm-password') }}</span>
                        </div>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Vai trò:</strong>
                        {!! Form::select('roles[]', $roles,[], array('value' => '{{ old("roles") }}', 'class' => 'form-control','multiple')) !!}
                        <span class="text-danger">{{ $errors->first('roles') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Xác nhận</button>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- /.card -->
</div>


@endsection
