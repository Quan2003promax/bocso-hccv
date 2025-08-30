@extends('layouts.app')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Sửa quyền</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Quyền</a></li>
                    <li class="breadcrumb-item active">Sửa</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sửa quyền: {{ $permission->name }}</h3>
            <div class="card-tools">
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
        
        <form method="POST" action="{{ route('permissions.update', $permission->id) }}">
            @csrf
            @method('PATCH')
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Tên quyền <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $permission->name) }}" 
                           placeholder="Ví dụ: user-create" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">
                        Tên quyền nên có định dạng: resource-action (ví dụ: user-create, role-edit)
                    </small>
                </div>

                <div class="form-group">
                    <label for="guard_name">Guard Name</label>
                    <input type="text" class="form-control @error('guard_name') is-invalid @enderror" 
                           id="guard_name" name="guard_name" value="{{ old('guard_name', $permission->guard_name) }}" 
                           placeholder="web">
                    @error('guard_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">
                        Để trống để sử dụng giá trị mặc định 'web'
                    </small>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật quyền
                </button>
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
