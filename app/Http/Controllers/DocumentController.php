<?php

namespace App\Http\Controllers;

use App\Models\ServiceRegistration;
use App\Services\DocumentConverterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    protected $documentConverter;

    public function __construct(DocumentConverterService $documentConverter)
    {
        $this->documentConverter = $documentConverter;
    }

    /**
     * Hiển thị thông tin tài liệu và nút tải về
     */
    public function view($id)
    {
        $registration = ServiceRegistration::findOrFail($id);
        
        if (!$registration->document_file) {
            abort(404, 'Không có tài liệu');
        }

        // Lấy thông tin file
        $fileInfo = $this->documentConverter->getFileInfo($registration);
        
        // Luôn hiển thị trang tải về cho tất cả loại file
        return view('documents.download', compact('registration', 'fileInfo'));
    }

    /**
     * API để lấy thông tin file
     */
    public function getFileInfo($id)
    {
        $registration = ServiceRegistration::findOrFail($id);
        
        if (!$registration->document_file) {
            return response()->json([
                'success' => false,
                'message' => 'Không có tài liệu'
            ], 404);
        }

        $fileInfo = $this->documentConverter->getFileInfo($registration);
        
        return response()->json([
            'success' => true,
            'data' => $fileInfo
        ]);
    }

    
    /**
     * Serve file publicly (không cần auth)
     */
    public function serveFile($filename)
    {
        try {
            // Validate filename để tránh path traversal
            if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
                \Log::warning('Suspicious filename detected', ['filename' => $filename]);
                abort(400, 'Tên file không hợp lệ');
            }
            
            // Kiểm tra file trong thư mục documents
            $filePath = 'documents/' . $filename;
            
            if (!Storage::disk('public')->exists($filePath)) {
                // Thử kiểm tra trong thư mục converted
                $convertedPath = 'documents/converted/' . $filename;
                if (Storage::disk('public')->exists($convertedPath)) {
                    $filePath = $convertedPath;
                } else {
                    \Log::error('File not found', [
                        'filename' => $filename,
                        'documents_path' => $filePath,
                        'converted_path' => $convertedPath,
                        'storage_path' => Storage::disk('public')->path('documents/')
                    ]);
                    abort(404, 'File không tồn tại: ' . $filename);
                }
            }
            
            $fullPath = Storage::disk('public')->path($filePath);
            
            if (!file_exists($fullPath)) {
                \Log::error('Physical file not found', [
                    'filename' => $filename,
                    'file_path' => $filePath,
                    'full_path' => $fullPath
                ]);
                abort(404, 'File không tồn tại: ' . $filename);
            }
            
            $mimeType = mime_content_type($fullPath);
            $fileSize = filesize($fullPath);
            
            $allowedMimeTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif',
                'image/bmp',
                'image/webp'
            ];
            
            if (!in_array($mimeType, $allowedMimeTypes)) {
                \Log::warning('Unsupported MIME type', [
                    'filename' => $filename,
                    'mime_type' => $mimeType
                ]);
                abort(415, 'Loại file không được hỗ trợ');
            }
            
            \Log::info('Serving file successfully', [
                'filename' => $filename,
                'file_path' => $filePath,
                'full_path' => $fullPath,
                'mime_type' => $mimeType,
                'file_size' => $fileSize
            ]);
            
            $safeFilename = preg_replace('/[^\w\-_\.]/', '_', $filename);
            
            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Content-Disposition' => 'inline; filename="' . $safeFilename . '"',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type',
                'Cache-Control' => 'public, max-age=3600', // Cache 1 hour
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error serving file: ' . $e->getMessage(), [
                'filename' => $filename,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Lỗi khi tải file: ' . $e->getMessage());
        }
    }
}
