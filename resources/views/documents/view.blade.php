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
                             class="max-w-full h-auto max-h-96 object-contain"
                             onload="console.log('Image loaded successfully')"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+CiAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkzhu5dpIGxvYWQgaGluaDwvdGV4dD4KICA8L3N2Zz4='; console.error('Image failed to load');">
                    </div>
                @elseif($fileInfo['mime_type'] === 'application/pdf')
                    <!-- Hiển thị PDF trong iframe -->
                    <iframe src="{{ $fileInfo['view_url'] }}" 
                            class="w-full h-screen min-h-[600px]"
                            frameborder="0"
                            onload="console.log('PDF iframe loaded successfully')"
                            onerror="console.error('PDF iframe failed to load')">
                        <p>Trình duyệt của bạn không hỗ trợ iframe. 
                           <a href="{{ $fileInfo['view_url'] }}" target="_blank" class="text-blue-600 underline">Nhấn vào đây để xem</a>
                        </p>
                    </iframe>
                @else
                    <!-- Fallback cho các loại file khác -->
                    <div class="flex flex-col items-center justify-center h-96 bg-gray-50">
                        <div class="text-center">
                            <i class="fas fa-file-alt text-gray-400 text-6xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">File không thể xem trực tiếp</h3>
                            <p class="text-sm text-gray-600 mb-4">Loại file: {{ $fileInfo['mime_type'] }}</p>
                            <a href="{{ $fileInfo['download_url'] }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md"
                               download="{{ $fileInfo['original_name'] }}">
                                <i class="fas fa-download mr-2"></i>
                                Tải về để xem
                            </a>
                        </div>
                    </div>
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
