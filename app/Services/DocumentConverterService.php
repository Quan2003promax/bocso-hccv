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

            // Thử convert bằng LibreOffice trước (nếu có) - tốt nhất cho phông chữ tiếng Việt
            $pdfFullPath = Storage::disk('public')->path($pdfFilePath);
            if ($this->convertWithLibreOffice($originalFilePath, $pdfFullPath)) {
                Log::info('Convert file thành công với LibreOffice', [
                    'original' => $filePath,
                    'pdf' => $pdfFilePath
                ]);
                return $pdfFilePath;
            }

            // Fallback: Sử dụng HTML intermediate với cấu hình Unicode tốt hơn
            Log::info('LibreOffice không khả dụng, sử dụng HTML intermediate với Unicode', [
                'file_path' => $filePath
            ]);

            // Load document
            $phpWord = IOFactory::load($originalFilePath);
            
            // Convert to HTML với encoding UTF-8
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            $htmlContent = $htmlWriter->getContent();
            
            // Cải thiện HTML để hỗ trợ Unicode và giữ nguyên bố cục
            $htmlContent = $this->fixVietnameseEncodingAndLayout($htmlContent);
            
            // Tạo file HTML tạm
            $htmlTempPath = storage_path('temp/' . uniqid() . '.html');
            file_put_contents($htmlTempPath, $htmlContent);
            
            // Convert HTML sang PDF với cấu hình Unicode
            $this->convertHtmlToPdfWithUnicode($htmlTempPath, $pdfFullPath);
            
            // Xóa file HTML tạm
            unlink($htmlTempPath);

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
                Log::info('LibreOffice not found, skipping LibreOffice conversion');
                return false;
            }

            // Tạo thư mục output nếu chưa có
            $outputDir = dirname($outputPath);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Command để convert với cấu hình tốt hơn cho tiếng Việt
            $command = sprintf(
                '"%s" --headless --convert-to pdf --outdir "%s" --infilter="writer8" "%s"',
                $libreOfficePath,
                $outputDir,
                $inputPath
            );

            Log::info('Executing LibreOffice command', [
                'command' => $command,
                'input_path' => $inputPath,
                'output_path' => $outputPath
            ]);

            // Thực thi command
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);

            // Kiểm tra file output
            $expectedOutputPath = $outputDir . '/' . pathinfo($inputPath, PATHINFO_FILENAME) . '.pdf';
            
            if ($returnCode === 0 && file_exists($expectedOutputPath)) {
                // Di chuyển file đến vị trí mong muốn
                if ($expectedOutputPath !== $outputPath) {
                    rename($expectedOutputPath, $outputPath);
                }
                
                Log::info('LibreOffice convert successful', [
                    'input' => $inputPath,
                    'output' => $outputPath,
                    'file_size' => filesize($outputPath)
                ]);
                
                return true;
            }

            Log::warning('LibreOffice convert failed', [
                'command' => $command,
                'output' => $output,
                'return_code' => $returnCode,
                'expected_output' => $expectedOutputPath,
                'file_exists' => file_exists($expectedOutputPath)
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
            // Windows - Các đường dẫn phổ biến
            'C:\Program Files\LibreOffice\program\soffice.exe',
            'C:\Program Files (x86)\LibreOffice\program\soffice.exe',
            'C:\laragon\bin\libreoffice\program\soffice.exe',
            'C:\laragon\bin\libreoffice\program\soffice.com',
            // Linux
            '/usr/bin/libreoffice',
            '/usr/bin/soffice',
            '/usr/local/bin/libreoffice',
            '/usr/local/bin/soffice',
            // macOS
            '/Applications/LibreOffice.app/Contents/MacOS/soffice'
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                Log::info('Found LibreOffice at: ' . $path);
                return $path;
            }
        }

        // Thử tìm trong PATH
        $output = [];
        if (PHP_OS_FAMILY === 'Windows') {
            exec('where soffice 2>nul', $output);
            if (empty($output)) {
                exec('where libreoffice 2>nul', $output);
            }
        } else {
            exec('which soffice 2>/dev/null', $output);
            if (empty($output)) {
                exec('which libreoffice 2>/dev/null', $output);
            }
        }
        
        if (!empty($output[0]) && file_exists($output[0])) {
            Log::info('Found LibreOffice in PATH: ' . $output[0]);
            return $output[0];
        }

        Log::warning('LibreOffice not found, will use PhpWord fallback');
        return null;
    }

    /**
     * Sửa lỗi encoding tiếng Việt và giữ nguyên bố cục
     */
    private function fixVietnameseEncodingAndLayout($htmlContent)
    {
        // Đảm bảo HTML có meta charset UTF-8
        if (strpos($htmlContent, '<meta charset') === false) {
            $htmlContent = str_replace('<head>', '<head><meta charset="UTF-8">', $htmlContent);
        }
        
        // Thêm CSS để hỗ trợ phông chữ tiếng Việt và giữ nguyên bố cục
        $css = '
        <style>
            body { 
                font-family: "DejaVu Sans", "Times New Roman", "Arial Unicode MS", sans-serif; 
                font-size: 12pt;
                line-height: 1.4;
                margin: 0;
                padding: 0;
                color: #000;
            }
            p, div, span, td, th { 
                font-family: "DejaVu Sans", "Times New Roman", "Arial Unicode MS", sans-serif; 
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin: 0;
                border-spacing: 0;
            }
            td, th {
                border: 1px solid #000;
                padding: 4px 6px;
                vertical-align: top;
                text-align: left;
            }
            p {
                margin: 0 0 6pt 0;
                text-align: justify;
            }
            .MsoNormal {
                margin: 0;
                text-align: justify;
            }
            .MsoTable {
                border-collapse: collapse;
                width: 100%;
                border-spacing: 0;
            }
            .MsoTable td, .MsoTable th {
                border: 1px solid #000;
                padding: 4px 6px;
                vertical-align: top;
            }
            /* Giữ nguyên formatting từ Word */
            b, strong { font-weight: bold; }
            i, em { font-style: italic; }
            u { text-decoration: underline; }
            /* Căn chỉnh văn bản */
            .MsoNormal[style*="text-align: center"] { text-align: center !important; }
            .MsoNormal[style*="text-align: right"] { text-align: right !important; }
            .MsoNormal[style*="text-align: justify"] { text-align: justify !important; }
            /* Font size */
            .MsoNormal[style*="font-size"] { font-size: inherit !important; }
            @page { 
                margin: 2cm; 
                size: A4;
            }
        </style>';
        
        $htmlContent = str_replace('</head>', $css . '</head>', $htmlContent);
        
        return $htmlContent;
    }

    /**
     * Convert HTML sang PDF với cấu hình Unicode
     */
    private function convertHtmlToPdfWithUnicode($htmlPath, $pdfPath)
    {
        // Cấu hình dompdf để hỗ trợ Unicode
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isFontSubsettingEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('fontCache', storage_path('fonts'));
        $options->set('tempDir', storage_path('temp'));
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultPaperSize', 'A4');
        $options->set('defaultPaperOrientation', 'portrait');
        $options->set('isCssFloatEnabled', true);
        $options->set('isJavascriptEnabled', false);
        $options->set('debugKeepTemp', false);
        $options->set('defaultMediaType', 'print');
        $options->set('dpi', 96);
        
   
        $dompdf = new \Dompdf\Dompdf($options);
        

        $htmlContent = file_get_contents($htmlPath);
        

        if (!mb_check_encoding($htmlContent, 'UTF-8')) {
            $htmlContent = mb_convert_encoding($htmlContent, 'UTF-8', 'auto');
        }
        
        $dompdf->loadHtml($htmlContent);
        
        // Render PDF
        $dompdf->render();
        
        // Save PDF
        $output = $dompdf->output();
        file_put_contents($pdfPath, $output);
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
            'filename' => $registration->document_original_name,
            'public_url' => $fileUrl,
            'route_name' => 'admin.documents.serve'
        ]);
        
        // Encode URL để sử dụng trong Google Docs Viewer
        $encodedUrl = urlencode($fileUrl);
        
        $viewerUrl = "https://docs.google.com/viewer?url={$encodedUrl}&embedded=true";
        
        \Log::info('Generated Google Docs Viewer URL', [
            'viewer_url' => $viewerUrl,
            'encoded_url' => $encodedUrl
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
            'filename' => $registration->document_original_name,
            'public_url' => $fileUrl,
            'route_name' => 'admin.documents.serve'
        ]);
        
        // Encode URL để sử dụng trong Microsoft Office Online Viewer
        $encodedUrl = urlencode($fileUrl);
        
        $viewerUrl = "https://view.officeapps.live.com/op/embed.aspx?src={$encodedUrl}";
        
        \Log::info('Generated Office Online Viewer URL', [
            'viewer_url' => $viewerUrl,
            'encoded_url' => $encodedUrl
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
            $downloadFilename = basename($registration->document_file);
            $downloadUrl = route('admin.documents.serve', ['filename' => $downloadFilename]);
            
            return [
                'can_view' => true,
                'view_url' => null, // Sẽ được set bởi controller
                'download_url' => $downloadUrl,
                'original_name' => $registration->document_original_name,
                'file_size' => $registration->formatted_file_size,
                'mime_type' => $registration->document_mime_type,
                'is_converted' => false,
                'viewer_type' => 'office_online'
            ];
        }
        
        if (!$viewablePath) {
            $downloadFilename = basename($registration->document_file);
            $downloadUrl = route('admin.documents.serve', ['filename' => $downloadFilename]);
            
            return [
                'can_view' => false,
                'download_url' => $downloadUrl,
                'original_name' => $registration->document_original_name,
                'file_size' => $registration->formatted_file_size,
                'mime_type' => $registration->document_mime_type
            ];
        }

        // Tạo URL cho file view thông qua route serve
        $filename = basename($viewablePath);
        $viewUrl = route('admin.documents.serve', ['filename' => $filename]);
        
        // Tạo URL cho file download
        $downloadFilename = basename($registration->document_file);
        $downloadUrl = route('admin.documents.serve', ['filename' => $downloadFilename]);

        return [
            'can_view' => true,
            'view_url' => Storage::url($viewablePath),
            'download_url' => Storage::url($registration->document_file),
            'original_name' => $registration->document_original_name,
            'file_size' => $registration->formatted_file_size,
            'mime_type' => $registration->document_mime_type,
            'is_converted' => $viewablePath !== $registration->document_file
        ];
    }
}
