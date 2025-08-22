@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Quản lý đăng ký dịch vụ
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="15%">Số thứ tự</th>
                                    <th width="20%">Họ và tên</th>
                                    <th width="15%">Phòng ban</th>
                                    <th width="15%">Thời gian đăng ký</th>
                                    <th width="15%">Trạng thái</th>
                                    <th width="15%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($registrations as $registration)
                                    <tr>
                                        <td>{{ $registration->id }}</td>
                                        <td>
                                            <span class="badge bg-primary fs-6">{{ $registration->queue_number }}</span>
                                        </td>
                                        <td>{{ $registration->full_name }}</td>
                                        <td>{{ $registration->department->name }}</td>
                                        <td>{{ $registration->created_at->format('H:i d/m/Y') }}</td>
                                        <td>
                                            @switch($registration->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                    @break
                                                @case('processing')
                                                    <span class="badge bg-info">Đang xử lý</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-success">Đã xử lý</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $registration->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.service-registrations.show', $registration) }}" 
                                               class="btn btn-sm btn-info me-2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.service-registrations.destroy', $registration) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa đăng ký này?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                                            <br>
                                            <span class="text-muted">Chưa có đăng ký nào</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($registrations->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $registrations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
