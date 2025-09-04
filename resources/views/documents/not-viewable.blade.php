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
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    Không thể xem trực tiếp tài liệu này
                </h3>
                
                <p class="text-sm text-gray-500 mb-6">
                    Tài liệu <strong>{{ $fileInfo['original_name'] }}</strong> không thể hiển thị trực tiếp trong trình duyệt.
                    Vui lòng tải về để xem nội dung.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
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

                <div class="flex justify-center space-x-4">
                    <a href="{{ $fileInfo['download_url'] }}" 
                       download="{{ $fileInfo['original_name'] }}"
                       class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-black rounded-md font-medium">
                        <i class="fas fa-download mr-2"></i>
                        Tải về tài liệu
                    </a>
                    
                                         @if(str_contains(strtolower($fileInfo['original_name']), '.doc') || str_contains(strtolower($fileInfo['original_name']), '.docx'))
                         <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-md">
                             <i class="fas fa-info-circle mr-2"></i>
                             <span class="text-sm">File DOC/DOCX - Sử dụng Microsoft Office Online để xem phông chữ tiếng Việt chính xác</span>
                         </div>
                         <div class="mt-2 space-x-2">
                             <a href="{{ route('admin.documents.view', $registration->id) }}" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                                 <i class="fas fa-eye mr-2"></i>
                                 Xem tài liệu
                             </a>
                             @php
                                 $officeOnlineUrl = app('App\Services\DocumentConverterService')->getOfficeOnlineViewerUrl($registration);
                             @endphp
                             @if($officeOnlineUrl)
                                 <a href="{{ $officeOnlineUrl }}" 
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                                     <i class="fas fa-external-link-alt mr-2"></i>
                                     Mở trong tab mới
                                 </a>
                             @endif
                         </div>
                     @endif
                </div>

                <div class="mt-6 text-xs text-gray-400">
                    <p>Để xem file DOC/DOCX, bạn có thể:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Tải về và mở bằng Microsoft Word hoặc ứng dụng tương tự</li>
                        <li>Sử dụng Google Docs để mở file online</li>
                        <li>Chuyển đổi file sang PDF trước khi upload</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
