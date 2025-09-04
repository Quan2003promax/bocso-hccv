@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-alt mr-2"></i>
                Xem tài liệu: {{ $fileInfo['original_name'] }}
            </h3>
            <div class="flex items-center space-x-2">
                @if($fileInfo['is_converted'])
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                        <i class="fas fa-sync-alt mr-1"></i>
                        Đã convert sang PDF
                    </span>
                @endif
                <a href="{{ $fileInfo['download_url'] }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 rounded-md"
                   download="{{ $fileInfo['original_name'] }}">
                    <i class="fas fa-download mr-2"></i>
                    Tải về
                </a>
                <a href="{{ route('admin.service-registrations.show', $registration->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <div class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div>
                        <span class="font-medium">Tên file:</span> {{ $fileInfo['original_name'] }}
                    </div>
                    <div>
                        <span class="font-medium">Kích thước:</span> {{ $fileInfo['file_size'] }}
                    </div>
                    <div>
                        <span class="font-medium">Loại file:</span> {{ $fileInfo['mime_type'] }}
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                @if(str_contains($fileInfo['mime_type'], 'image'))
                    <!-- Hiển thị hình ảnh -->
                    <div class="flex justify-center p-4">
                        <img src="{{ $fileInfo['view_url'] }}" 
                             alt="{{ $fileInfo['original_name'] }}"
                             class="max-w-full h-auto max-h-96 object-contain">
                    </div>
                @else
                    <!-- Hiển thị PDF trong iframe -->
                    <iframe src="{{ $fileInfo['view_url'] }}" 
                            class="w-full h-screen min-h-[600px]"
                            frameborder="0">
                        <p>Trình duyệt của bạn không hỗ trợ iframe. 
                           <a href="{{ $fileInfo['view_url'] }}" target="_blank">Nhấn vào đây để xem</a>
                        </p>
                    </iframe>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    /* Đảm bảo iframe hiển thị đúng */
    iframe {
        border: none;
        width: 100%;
        height: 80vh;
        min-height: 600px;
    }
</style>
@endsection
