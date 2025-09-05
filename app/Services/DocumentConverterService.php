<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class DocumentConverterService
{
    /**
     * Convert doc/docx file to PDF
     */
    public function convertToPdf($filePath, $originalName)
    {
        try {
            // Kiểm tra file có tồn tại không
            if (!Storage::disk('public')->exists($filePath)) {
                throw new \Exception('File không tồn tại');
            }

            $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            
            // Chỉ xử lý file doc/docx
            if (!in_array($fileExtension, ['doc', 'docx'])) {
                throw new \Exception('Chỉ hỗ trợ file DOC/DOCX');
            }

            // Đường dẫn đầy đủ đến file gốc
            $originalFilePath = Storage::disk('public')->path($filePath);
            
            // Tạo tên file PDF mới
            $pdfFileName = pathinfo($filePath, PATHINFO_FILENAME) . '_converted.pdf';
            $pdfFilePath = 'documents/converted/' . $pdfFileName;
            
            // Kiểm tra xem file PDF đã tồn tại chưa
            if (Storage::disk('public')->exists($pdfFilePath)) {
                Log::info('PDF đã tồn tại, sử dụng file cũ', [
                    'pdf_path' => $pdfFilePath
                ]);
                return $pdfFilePath;
            }

            // Tạo thư mục converted nếu chưa có
            Storage::disk('public')->makeDirectory('documents/converted');

            Log::info('Bắt đầu convert file DOC/DOCX sang PDF', [
                'original' => $filePath,
                'target' => $pdfFilePath
            ]);

            // Thử convert bằng LibreOffice trước (nếu có)
            $pdfFullPath = Storage::disk('public')->path($pdfFilePath);
            if ($this->convertWithLibreOffice($originalFilePath, $pdfFullPath)) {
                Log::info('Convert file thành công với LibreOffice', [
                    'original' => $filePath,
                    'pdf' => $pdfFilePath
                ]);
                return $pdfFilePath;
            }

            // Fallback: Sử dụng PhpWord với dompdf và cấu hình Unicode
            Log::info('LibreOffice không khả dụng, sử dụng PhpWord với Unicode', [
                'file_path' => $filePath
            ]);

            // Cấu hình PhpWord với dompdf
            Settings::setPdfRenderer(Settings::PDF_RENDERER_DOMPDF, base_path('vendor/dompdf/dompdf'));
            
            // Cấu hình dompdf để hỗ trợ Unicode
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isFontSubsettingEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('fontCache', storage_path('fonts'));
            $options->set('tempDir', storage_path('temp'));
            
            // Tạo instance dompdf với options
            $dompdf = new \Dompdf\Dompdf($options);
            
            // Load document
            $phpWord = IOFactory::load($originalFilePath);
            
            // Convert to PDF
            $xmlWriter = IOFactory::createWriter($phpWord, 'PDF');
            $xmlWriter->save($pdfFullPath);

            Log::info('Convert file thành công với PhpWord', [
                'original' => $filePath,
                'pdf' => $pdfFilePath
            ]);

            return $pdfFilePath;

        } catch (\Exception $e) {
            Log::error('Lỗi convert file: ' . $e->getMessage(), [
                'file_path' => $filePath,
                'original_name' => $originalName
            ]);
            
            throw $e;
        }
    }

    /**
     * Convert file bằng LibreOffice (nếu có)
     */
    private function convertWithLibreOffice($inputPath, $outputPath)
    {
        try {
            // Kiểm tra LibreOffice có sẵn không
            $libreOfficePath = $this->findLibreOffice();
            if (!$libreOfficePath) {
                return false;
            }

            // Tạo thư mục output nếu chưa có
            $outputDir = dirname($outputPath);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Command để convert
            $command = sprintf(
                '"%s" --headless --convert-to pdf --outdir "%s" "%s"',
                $libreOfficePath,
                $outputDir,
                $inputPath
            );

            // Thực thi command
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($outputPath)) {
                return true;
            }

            Log::warning('LibreOffice convert failed', [
                'command' => $command,
                'output' => $output,
                'return_code' => $returnCode
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Lỗi LibreOffice convert: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tìm đường dẫn LibreOffice
     */
    private function findLibreOffice()
    {
        $possiblePaths = [
            // Windows
            'C:\Program Files\LibreOffice\program\soffice.exe',
            'C:\Program Files (x86)\LibreOffice\program\soffice.exe',
            // Linux
            '/usr/bin/libreoffice',
            '/usr/bin/soffice',
            // macOS
            '/Applications/LibreOffice.app/Contents/MacOS/soffice'
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Thử tìm trong PATH
        $output = [];
        exec('where soffice 2>/dev/null', $output);
        if (!empty($output[0])) {
            return $output[0];
        }

        return null;
    }

    /**
     * Kiểm tra xem file có thể xem trực tiếp không
     */
    public function canViewDirectly($mimeType)
    {
        $viewableTypes = [
            'application/pdf',
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif'
        ];

        return in_array($mimeType, $viewableTypes);
    }

    /**
     * Lấy đường dẫn file để xem
     */
    public function getViewableFilePath($registration)
    {
        if (!$registration->document_file) {
            return null;
        }

        $mimeType = $registration->document_mime_type;
        $filePath = $registration->document_file;
        $originalName = $registration->document_original_name;

        // Nếu file có thể xem trực tiếp
        if ($this->canViewDirectly($mimeType)) {
            return $filePath;
        }

        // Đối với file doc/docx, không thể xem trực tiếp nhưng có thể xem qua viewer
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (in_array($fileExtension, ['doc', 'docx'])) {
            // Return một giá trị đặc biệt để controller biết đây là file có thể xem qua viewer
            return 'viewer_supported';
        }

        return null;
    }

    /**
     * Lấy URL Google Docs Viewer cho file DOC/DOCX
     */
    public function getGoogleDocsViewerUrl($registration)
    {
        if (!$registration->document_file) {
            return null;
        }

        $fileExtension = strtolower(pathinfo($registration->document_original_name, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, ['doc', 'docx'])) {
            return null;
        }

        // Tạo URL đầy đủ đến file thông qua route public
        // Dùng path đầy đủ để route serve có thể truy xuất đúng file trong thư mục con
        $path = ltrim($registration->document_file, '/');
        $fileUrl = url(route('admin.documents.serve', ['path' => $path]));
        
        // Debug: Log URL để kiểm tra
        \Log::info('Google Docs Viewer URL generation', [
            'file_path' => $registration->document_file,
            'filename' => $filename,
            'public_url' => $fileUrl
        ]);
        
        // Encode URL để sử dụng trong Google Docs Viewer
        $encodedUrl = urlencode($fileUrl);
        
        $viewerUrl = "https://docs.google.com/viewer?url={$encodedUrl}&embedded=true";
        
        \Log::info('Generated Google Docs Viewer URL', [
            'viewer_url' => $viewerUrl
        ]);
        
        return $viewerUrl;
    }

    /**
     * Lấy URL Microsoft Office Online Viewer cho file DOC/DOCX
     */
    public function getOfficeOnlineViewerUrl($registration)
    {
        if (!$registration->document_file) {
            return null;
        }

        $fileExtension = strtolower(pathinfo($registration->document_original_name, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, ['doc', 'docx'])) {
            return null;
        }

        // Tạo URL đầy đủ đến file thông qua route public
        $path = ltrim($registration->document_file, '/');
        $fileUrl = url(route('admin.documents.serve', ['path' => $path]));
        
        // Debug: Log URL để kiểm tra
        \Log::info('Office Online Viewer URL generation', [
            'file_path' => $registration->document_file,
            'filename' => $filename,
            'public_url' => $fileUrl
        ]);
        
        // Encode URL để sử dụng trong Microsoft Office Online Viewer
        $encodedUrl = urlencode($fileUrl);
        
        $viewerUrl = "https://view.officeapps.live.com/op/embed.aspx?src={$encodedUrl}";
        
        \Log::info('Generated Office Online Viewer URL', [
            'viewer_url' => $viewerUrl
        ]);
        
        return $viewerUrl;
    }

    /**
     * Lấy thông tin file để hiển thị
     */
    public function getFileInfo($registration)
    {
        if (!$registration->document_file) {
            return null;
        }

        $viewablePath = $this->getViewableFilePath($registration);
        
        // Kiểm tra nếu là file DOC/DOCX có thể xem qua viewer
        $fileExtension = strtolower(pathinfo($registration->document_original_name, PATHINFO_EXTENSION));
        if (in_array($fileExtension, ['doc', 'docx'])) {
            return [
                'can_view' => true,
                'view_url' => null, // Sẽ được set bởi controller
                'download_url' => Storage::url($registration->document_file),
                'original_name' => $registration->document_original_name,
                'file_size' => $registration->formatted_file_size,
                'mime_type' => $registration->document_mime_type,
                'is_converted' => false,
                'viewer_type' => 'office_online'
            ];
        }
        
        if (!$viewablePath) {
            return [
                'can_view' => false,
                'download_url' => Storage::url($registration->document_file),
                'original_name' => $registration->document_original_name,
                'file_size' => $registration->formatted_file_size,
                'mime_type' => $registration->document_mime_type
            ];
        }

        return [
            'can_view' => true,
            // Dùng route serve để gắn header inline, kể cả ảnh/PDF
            'view_url' => url(route('admin.documents.serve', ['path' => ltrim($viewablePath, '/')])) ,
            'download_url' => Storage::url($registration->document_file),
            'original_name' => $registration->document_original_name,
            'file_size' => $registration->formatted_file_size,
            'mime_type' => $registration->document_mime_type,
            'is_converted' => $viewablePath !== $registration->document_file
        ];
    }
}
