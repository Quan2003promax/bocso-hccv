@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-pdf mr-2"></i>
                Xem tài liệu: {{ $fileInfo['original_name'] }}
            </h3>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                    <i class="fas fa-file-pdf mr-1"></i>
                    PDF
                </span>
                <a href="{{ $fileInfo['download_url'] }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700     rounded-md"
                   download="{{ $fileInfo['original_name'] }}">
                    <i class="fas fa-download mr-2"></i>
                    Tải về gốc
                </a>
                <a href="{{ $fileInfo['pdf_url'] }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700   rounded-md"
                   download="{{ pathinfo($fileInfo['original_name'], PATHINFO_FILENAME) }}_converted.pdf">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Tải PDF
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
                <div class="bg-green-50 px-4 py-2 border-b border-gray-200">
                    <div class="flex items-center text-sm text-green-700">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>File đã được convert sang PDF để hiển thị - Phông chữ tiếng Việt sẽ được hiển thị chính xác</span>
                    </div>
                </div>
                
                <iframe src="{{ $fileInfo['pdf_url'] }}" 
                        class="w-full h-screen min-h-[600px]"
                        frameborder="0">
                    <p>Trình duyệt của bạn không hỗ trợ iframe. 
                       <a href="{{ $fileInfo['pdf_url'] }}" target="_blank">Nhấn vào đây để xem</a>
                    </p>
                </iframe>
            </div>

            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-blue-600 mt-1 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Lưu ý:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>File DOC/DOCX đã được convert sang PDF để đảm bảo hiển thị chính xác</li>
                            <li>Bạn có thể tải về file PDF đã convert hoặc file gốc</li>
                            <li>Phông chữ tiếng Việt sẽ được hiển thị chính xác trong PDF</li>
                        </ul>
                    </div>
                </div>
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
