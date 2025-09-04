# Hướng dẫn hoàn thiện tính năng Convert DOC/DOCX sang PDF

## Tình trạng hiện tại

Hiện tại hệ thống đã có:
- ✅ Giao diện xem tài liệu
- ✅ Xử lý file PDF và hình ảnh
- ✅ Thông báo cho file DOC/DOCX không thể xem trực tiếp
- ✅ Nút tải về file gốc
- ⏳ Tính năng convert DOC/DOCX sang PDF (đang phát triển)

## Để hoàn thiện tính năng convert

### 1. Cài đặt thư viện cần thiết

```bash
# Cài đặt dompdf để convert sang PDF
composer require dompdf/dompdf

# Đã cài đặt sẵn phpword
composer require phpoffice/phpword
```
composer require spatie/browsershot
### 2. Cập nhật DocumentConverterService

Sau khi cài đặt dompdf, cập nhật file `app/Services/DocumentConverterService.php`:

```php
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
                return $pdfFilePath;
            }

            // Tạo thư mục converted nếu chưa có
            Storage::disk('public')->makeDirectory('documents/converted');

            // Cấu hình PhpWord với dompdf
            Settings::setPdfRenderer(Settings::PDF_RENDERER_DOMPDF, base_path('vendor/dompdf/dompdf'));
            
            // Load document
            $phpWord = IOFactory::load($originalFilePath);
            
            // Đường dẫn đầy đủ đến file PDF
            $pdfFullPath = Storage::disk('public')->path($pdfFilePath);
            
            // Convert to PDF
            $xmlWriter = IOFactory::createWriter($phpWord, 'PDF');
            $xmlWriter->save($pdfFullPath);

            Log::info('Convert file thành công', [
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

    // ... các method khác giữ nguyên
}
```

### 3. Cập nhật getViewableFilePath method

```php
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

    // Nếu là file doc/docx, thử convert sang PDF
    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (in_array($fileExtension, ['doc', 'docx'])) {
        try {
            $pdfPath = $this->convertToPdf($filePath, $originalName);
            return $pdfPath;
        } catch (\Exception $e) {
            Log::error('Không thể convert file: ' . $e->getMessage());
            return null;
        }
    }

    return null;
}
```

### 4. Tạo Job để xử lý convert bất đồng bộ (tùy chọn)

Để tránh timeout khi convert file lớn, có thể tạo job:

```bash
php artisan make:job ConvertDocumentToPdf
```

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\DocumentConverterService;
use App\Models\ServiceRegistration;

class ConvertDocumentToPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $registrationId;

    public function __construct($registrationId)
    {
        $this->registrationId = $registrationId;
    }

    public function handle(DocumentConverterService $converter)
    {
        $registration = ServiceRegistration::find($this->registrationId);
        
        if (!$registration || !$registration->document_file) {
            return;
        }

        try {
            $converter->convertToPdf(
                $registration->document_file, 
                $registration->document_original_name
            );
        } catch (\Exception $e) {
            \Log::error('Lỗi convert document: ' . $e->getMessage());
        }
    }
}
```

### 5. Cấu hình Queue (nếu sử dụng job)

```bash
# Chạy queue worker
php artisan queue:work

# Hoặc sử dụng supervisor để chạy queue liên tục
```

## Tính năng đã hoàn thành

1. **Giao diện xem tài liệu**: 
   - Trang xem tài liệu với iframe cho PDF
   - Hiển thị hình ảnh trực tiếp
   - Thông báo cho file không thể xem

2. **Quản lý tài liệu**:
   - Nút "Xem tài liệu" và "Tải về" trong danh sách
   - Thông tin chi tiết file trong trang show
   - Xử lý file an toàn

3. **Tương thích trình duyệt**:
   - Hỗ trợ xem PDF trực tiếp
   - Fallback cho trình duyệt không hỗ trợ iframe

## Lưu ý

- File PDF được convert sẽ được lưu trong thư mục `storage/app/public/documents/converted/`
- Mỗi file chỉ convert một lần, lần sau sẽ sử dụng file đã convert
- Cần đảm bảo server có đủ quyền ghi vào thư mục storage
- Với file lớn, nên sử dụng queue để tránh timeout

## Testing

Sau khi hoàn thiện, test với:
1. File PDF: Xem trực tiếp
2. File hình ảnh: Hiển thị trực tiếp  
3. File DOC/DOCX: Convert và xem PDF
4. File không hỗ trợ: Hiển thị thông báo
