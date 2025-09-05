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
     * Hiển thị tài liệu trong iframe
     */
    public function view($id)
    {
        $registration = ServiceRegistration::findOrFail($id);
        
        if (!$registration->document_file) {
            abort(404, 'Không có tài liệu');
        }

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
                    $filename = basename($pdfPath);
                    $fileInfo['pdf_url'] = route('admin.documents.serve', ['filename' => $filename]);
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
        // Nếu là PDF/ảnh và đang nằm trong public storage, đảm bảo dùng route serve để có header inline
        if (!empty($fileInfo['pdf_url'])) {
            $basename = basename(parse_url($fileInfo['pdf_url'], PHP_URL_PATH));
            $fileInfo['pdf_url'] = route('admin.documents.serve', ['filename' => $basename]);
        }
        
        if (!$fileInfo['can_view']) {
            // Nếu không thể xem trực tiếp, hiển thị trang thông báo
            return view('documents.not-viewable', compact('registration', 'fileInfo'));
        }

        return view('documents.view', compact('registration', 'fileInfo'));
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
     * Convert file doc/docx sang PDF
     */
    public function convertToPdf($id)
    {
        $registration = ServiceRegistration::findOrFail($id);
        
        if (!$registration->document_file) {
            return response()->json([
                'success' => false,
                'message' => 'Không có tài liệu'
            ], 404);
        }

        try {
            $pdfPath = $this->documentConverter->convertToPdf(
                $registration->document_file, 
                $registration->document_original_name
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Convert thành công',
                'pdf_url' => Storage::url($pdfPath)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi convert file: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Serve file publicly (không cần auth)
     */
    public function serveFile($filename)
    {
        $filePath = 'documents/' . $filename;
        
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File không tồn tại');
        }
        
        $fullPath = Storage::disk('public')->path($filePath);
        $mimeType = mime_content_type($fullPath);
        $isInlineRenderable = in_array($mimeType, [
            'application/pdf',
            'image/png', 'image/jpeg', 'image/gif', 'image/webp', 'image/svg+xml'
        ]);

        $disposition = ($isInlineRenderable ? 'inline' : 'attachment') . '; filename="' . basename($fullPath) . '"';

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition,
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'no-referrer-when-downgrade',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type',
        ]);
    }
}
