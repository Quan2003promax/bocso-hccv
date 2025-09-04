@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-alt mr-2"></i>
                Tài liệu: {{ $fileInfo['original_name'] }}
            </h3>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                    <i class="fas fa-file-word mr-1"></i>
                    File DOC/DOCX
                </span>
                <a href="{{ route('admin.service-registrations.show', $registration->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
                    <i class="fas fa-file-word text-blue-600 text-2xl"></i>
                </div>
                
                <h3 class="text-xl font-medium text-gray-900 mb-2">
                    File {{ strtoupper(pathinfo($fileInfo['original_name'], PATHINFO_EXTENSION)) }}
                </h3>
                
                <p class="text-sm text-gray-500">
                    Tài liệu <strong>{{ $fileInfo['original_name'] }}</strong> cần được xem bằng ứng dụng hỗ trợ
                </p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-4">
                    <div>
                        <span class="font-medium text-gray-700">Tên file:</span>
                        <div class="text-gray-600">{{ $fileInfo['original_name'] }}</div>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Kích thước:</span>
                        <div class="text-gray-600">{{ $fileInfo['file_size'] }}</div>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Loại file:</span>
                        <div class="text-gray-600">{{ $fileInfo['mime_type'] }}</div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center space-x-4 mb-6">
                <a href="{{ $fileInfo['download_url'] }}" 
                   download="{{ $fileInfo['original_name'] }}"
                   class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                    <i class="fas fa-download mr-2"></i>
                    Tải về tài liệu
                </a>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-blue-600 mt-1 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-3">Cách xem file DOC/DOCX:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium mb-2">1. Xem trực tiếp:</h4>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Tải về và mở bằng Microsoft Word</li>
                                    <li>Sử dụng LibreOffice Writer (miễn phí)</li>
                                    <li>Mở bằng Google Docs (upload file)</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium mb-2">2. Xem online:</h4>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Microsoft Office Online</li>
                                    <li>Google Docs Viewer</li>
                                    <li>Zoho Docs</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-medium mb-1">Lưu ý:</p>
                        <p>File DOC/DOCX chứa nội dung tiếng Việt sẽ hiển thị chính xác khi mở bằng các ứng dụng hỗ trợ Unicode. 
                        Hãy tải về và mở bằng Microsoft Word hoặc ứng dụng tương tự để xem nội dung đầy đủ.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
