@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-alt mr-2"></i>
                Tài liệu: {{ $fileInfo['original_name'] }}
            </h3>
            <a href="{{ route('admin.service-registrations.show', $registration->id) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
        </div>
        
        <div class="p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
                    <i class="fas fa-download text-blue-600 text-2xl"></i>
                </div>
                
                <h3 class="text-xl font-medium text-gray-900 mb-4">
                    Tải về tài liệu
                </h3>
                
                <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
                    Tài liệu <strong>{{ $fileInfo['original_name'] }}</strong> đã sẵn sàng để tải về. 
                    Nhấn nút bên dưới để tải file về máy tính của bạn.
                </p>

                <div class="bg-gray-50 rounded-lg p-6 mb-8 max-w-2xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                        <div class="text-center">
                            <div class="font-medium text-gray-700 mb-2">Tên file</div>
                            <div class="text-gray-600 break-all">{{ $fileInfo['original_name'] }}</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-700 mb-2">Kích thước</div>
                            <div class="text-gray-600">{{ $fileInfo['file_size'] }}</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-700 mb-2">Loại file</div>
                            <div class="text-gray-600">{{ $fileInfo['mime_type'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center space-x-4 mb-8">
                    <a href="{{ $fileInfo['download_url'] }}" 
                       download="{{ $fileInfo['original_name'] }}"
                       class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-lg shadow-lg hover:shadow-xl transition-all duration-200">
                        <i class="fas fa-download mr-3 text-xl"></i>
                        Tải về tài liệu
                    </a>
                </div>

                @if(str_contains(strtolower($fileInfo['original_name']), '.doc') || str_contains(strtolower($fileInfo['original_name']), '.docx'))
                    <div class="mt-8 p-6 bg-blue-50 border border-blue-200 rounded-lg max-w-2xl mx-auto">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-1 mr-3 text-lg"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-2">Lưu ý cho file DOC/DOCX:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Để xem file với phông chữ tiếng Việt chính xác, hãy mở bằng Microsoft Word</li>
                                    <li>Bạn cũng có thể sử dụng Google Docs hoặc LibreOffice Writer</li>
                                    <li>Nếu gặp lỗi hiển thị phông chữ, hãy cài đặt font tiếng Việt trên máy tính</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-6 text-xs text-gray-400 max-w-2xl mx-auto">
                    <p class="mb-2">Hướng dẫn sử dụng:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Nhấn "Tải về tài liệu" để tải file về máy tính</li>
                        <li>Mở file bằng ứng dụng phù hợp (Word, PDF Reader, v.v.)</li>
                        <li>Nếu cần hỗ trợ, vui lòng liên hệ quản trị viên</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
