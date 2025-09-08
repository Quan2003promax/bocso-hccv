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
        // Kiểm tra nếu là file DOC/DOCX, thử convert sang PDF trước
        $fileExtension = strtolower(pathinfo($registration->document_original_name, PATHINFO_EXTENSION));
        if (in_array($fileExtension, ['doc', 'docx'])) {
            try {
                // Thử convert sang PDF
                $pdfPath = $this->documentConverter->convertToPdf(
                    $registration->document_file, 
                    $registration->document_original_name
                );
                
                if ($pdfPath) {
                    $fileInfo = $this->documentConverter->getFileInfo($registration);
                    $path = ltrim($pdfPath, '/');
                    $fileInfo['pdf_url'] = route('admin.documents.serve', ['path' => $path]);
                    $fileInfo['is_converted'] = true;
                    
                    \Log::info('Converted DOC/DOCX to PDF successfully', [
                        'id' => $registration->id,
                        'file_name' => $registration->document_original_name,
                        'pdf_path' => $pdfPath,
                        'pdf_url' => $fileInfo['pdf_url']
                    ]);
                    
                    return view('documents.view-pdf', compact('registration', 'fileInfo'));
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to convert DOC/DOCX to PDF, falling back to Google Docs Viewer', [
                    'id' => $registration->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Fallback: Sử dụng Google Docs Viewer
            $googleDocsUrl = $this->documentConverter->getGoogleDocsViewerUrl($registration);
            if ($googleDocsUrl) {
                $fileInfo = $this->documentConverter->getFileInfo($registration);
                $fileInfo['google_docs_url'] = $googleDocsUrl;
                
                \Log::info('Using Google Docs Viewer as fallback', [
                    'id' => $registration->id,
                    'file_name' => $registration->document_original_name,
                    'google_docs_url' => $googleDocsUrl
                ]);
                
                return view('documents.view-google-docs', compact('registration', 'fileInfo'));
            }
        }
        
        // Đối với các file khác, kiểm tra xem có thể xem trực tiếp không
        $fileInfo = $this->documentConverter->getFileInfo($registration);
        // Chuẩn hoá URL đi qua route serve để gắn header inline trong iframe
        if (!empty($fileInfo['pdf_url'])) {
            $path = ltrim(parse_url($fileInfo['pdf_url'], PHP_URL_PATH), '/');
            $fileInfo['pdf_url'] = route('admin.documents.serve', ['path' => $path]);
        }
        if (!empty($fileInfo['view_url'])) {
            $path = ltrim(parse_url($fileInfo['view_url'], PHP_URL_PATH), '/');
            $fileInfo['view_url'] = route('admin.documents.serve', ['path' => $path]);
        }
        
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
    public function serveFile($path)
    {
        try {
            // Extract filename from path (handle both single filename and nested paths)
            $filename = basename($path);
            
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
                'filename' => $filename ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Lỗi khi tải file: ' . $e->getMessage());
        }
    }
}
