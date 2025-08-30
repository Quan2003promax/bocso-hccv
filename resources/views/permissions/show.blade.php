@extends('layouts.app')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Chi tiết quyền</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Quyền</a></li>
                    <li class="breadcrumb-item active">Chi tiết</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thông tin quyền: {{ $permission->name }}</h3>
            <div class="card-tools">
                @can('permission-edit')
                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                @endcan
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">ID:</th>
                            <td>{{ $permission->id }}</td>
                        </tr>
                        <tr>
                            <th>Tên quyền:</th>
                            <td>{{ $permission->name }}</td>
                        </tr>
                        <tr>
                            <th>Guard Name:</th>
                            <td>{{ $permission->guard_name }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tạo:</th>
                            <td>{{ $permission->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Ngày cập nhật:</th>
                            <td>{{ $permission->updated_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h5>Vai trò sử dụng quyền này:</h5>
                    @if($permission->roles->count() > 0)
                        <div class="list-group">
                            @foreach($permission->roles as $role)
                                <div class="list-group-item">
                                    <h6 class="mb-1">{{ $role->name }}</h6>
                                    <small>ID: {{ $role->id }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Chưa có vai trò nào sử dụng quyền này.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-footer">
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>
</div>
@endsection
