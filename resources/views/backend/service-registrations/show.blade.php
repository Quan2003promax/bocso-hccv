@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-eye mr-2"></i>
                Chi tiết đăng ký dịch vụ
            </h3>
            <a href="{{ route('admin.service-registrations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
        </div>
        <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white border border-gray-200 rounded-md">
                        <div class="px-4 py-3 border-b flex items-center">
                            <h5 class="font-medium text-gray-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Thông tin đăng ký
                            </h5>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm text-gray-500">Số thứ tự</div>
                                    <div class="mt-1"><span class="inline-flex px-3 py-1 rounded-full bg-blue-100 text-blue-800 font-semibold">{{ $registration->queue_number }}</span></div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Trạng thái</div>
                                    <div class="mt-1">
                                        @switch($registration->status)
                                            @case('pending')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Chờ xử lý</span>
                                                @break
                                            @case('received')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Đã tiếp nhận</span>
                                                @break
                                            @case('processing')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-200 text-blue-800">Đang xử lý</span>
                                                @break
                                            @case('completed')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Đã xử lý</span>
                                                @break
                                            @case('cancelled')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Đã hủy</span>
                                                @break
                                            @case('returned')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Trả hồ sơ</span>
                                                @break
                                            @default
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $registration->status }}</span>
                                        @endswitch
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Họ và tên</div>
                                    <div class="mt-1">{{ $registration->full_name }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Năm sinh</div>
                                    <div class="mt-1">{{ $registration->birth_year }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Số căn cước công dân</div>
                                    <div class="mt-1">{{ $registration->identity_number }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Phòng ban</div>
                                    <div class="mt-1">{{ $registration->department->name }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Thời gian đăng ký</div>
                                    <div class="mt-1">{{ $registration->created_at->format('H:i d/m/Y') }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Cập nhật lần cuối</div>
                                    <div class="mt-1">{{ $registration->updated_at->format('H:i d/m/Y') }}</div>
                                </div>
                                @if($registration->document_file)
                                    <div class="md:col-span-2">
                                        <div class="text-sm text-gray-500">Tài liệu đính kèm</div>
                                        <div class="mt-2">
                                            <div class="flex items-center space-x-2">
                                                @php
                                                    $fileExtension = strtolower(pathinfo($registration->document_original_name, PATHINFO_EXTENSION));
                                                    $canView = in_array($fileExtension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                                                @endphp
                                                @php
                                                    $filename = basename($registration->document_file);
                                                    $fileUrl = route('admin.documents.serve', ['filename' => $filename]);
                                                @endphp
                                                @if($canView)
                                                    <a href="{{ $fileUrl }}" 
                                                       target="_blank"
                                                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md bg-blue-100 text-blue-800 hover:bg-blue-200" style="margin-right: 10px;">
                                                        <i class="fas fa-eye mr-2"></i>
                                                        Xem tài liệu
                                                    </a>
                                                @endif
                                                <a href="{{ $fileUrl }}" 
                                                   download="{{ $registration->document_original_name }}"
                                                   class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md bg-green-100 text-green-800 hover:bg-green-200">
                                                    <i class="fas fa-download mr-2"></i>
                                                    Tải về
                                                </a>
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500">
                                                {{ $registration->document_original_name }} ({{ $registration->formatted_file_size }})
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($registration->notes)
                                <div class="mt-4">
                                    <div class="text-sm text-gray-500">Ghi chú</div>
                                    <div class="mt-1">{{ $registration->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-white border border-gray-200 rounded-md">
                        <div class="px-4 py-3 border-b flex items-center">
                            <h5 class="font-medium text-gray-800">
                                <i class="fas fa-edit mr-2"></i>
                                Cập nhật trạng thái
                            </h5>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('admin.service-registrations.update-status', $registration) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái mới</label>
                                    <select id="status" name="status" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="pending" {{ $registration->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                        <option value="received" {{ $registration->status == 'received' ? 'selected' : '' }}>Đã tiếp nhận</option>
                                        <option value="processing" {{ $registration->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="completed" {{ $registration->status == 'completed' ? 'selected' : '' }}>Đã xử lý</option>
                                        <option value="cancelled" {{ $registration->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                        <option value="returned" {{ $registration->status == 'returned' ? 'selected' : '' }}>Trả hồ sơ</option>
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Ghi chú</label>
                                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Nhập ghi chú (nếu có)">{{ $registration->notes }}</textarea>
                                </div>
                                <div class="mt-4 text-right">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-black
                                     hover:bg-blue-700">
                                        <i class="fas fa-save mr-2"></i>
                                        Cập nhật trạng thái
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
