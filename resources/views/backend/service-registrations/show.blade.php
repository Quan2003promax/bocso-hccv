@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-eye me-2"></i>
                            Chi tiết đăng ký dịch vụ
                        </h3>
                        <a href="{{ route('admin.service-registrations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Thông tin đăng ký
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Số thứ tự:</label>
                                            <div class="form-control-plaintext">
                                                <span class="badge bg-primary fs-5">{{ $registration->queue_number }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Trạng thái:</label>
                                            <div class="form-control-plaintext">
                                                @switch($registration->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning fs-6">Chờ xử lý</span>
                                                        @break
                                                    @case('processing')
                                                        <span class="badge bg-info fs-6">Đang xử lý</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-success fs-6">Đã xử lý</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-danger fs-6">Đã hủy</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary fs-6">{{ $registration->status }}</span>
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Họ và tên:</label>
                                            <div class="form-control-plaintext">{{ $registration->full_name }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Năm sinh:</label>
                                            <div class="form-control-plaintext">{{ $registration->birth_year }}</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Số căn cước công dân:</label>
                                            <div class="form-control-plaintext">{{ $registration->identity_number }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Phòng ban:</label>
                                            <div class="form-control-plaintext">{{ $registration->department->name }}</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Thời gian đăng ký:</label>
                                            <div class="form-control-plaintext">{{ $registration->created_at->format('H:i d/m/Y') }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Cập nhật lần cuối:</label>
                                            <div class="form-control-plaintext">{{ $registration->updated_at->format('H:i d/m/Y') }}</div>
                                        </div>
                                    </div>
                                    @if($registration->notes)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Ghi chú:</label>
                                            <div class="form-control-plaintext">{{ $registration->notes }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-edit me-2"></i>
                                        Cập nhật trạng thái
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.service-registrations.update-status', $registration) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Trạng thái mới:</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending" {{ $registration->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                                <option value="processing" {{ $registration->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                                <option value="completed" {{ $registration->status == 'completed' ? 'selected' : '' }}>Đã xử lý</option>
                                                <option value="cancelled" {{ $registration->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Ghi chú:</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                                      placeholder="Nhập ghi chú (nếu có)">{{ $registration->notes }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save me-2"></i>
                                            Cập nhật trạng thái
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
