<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\ServiceRegistration;

class ProcessDocumentUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $registrationId;
    protected $filePath;
    protected $originalName;

    /**
     * Create a new job instance.
     */
    public function __construct($registrationId, $filePath, $originalName)
    {
        $this->registrationId = $registrationId;
        $this->filePath = $filePath;
        $this->originalName = $originalName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $registration = ServiceRegistration::find($this->registrationId);
            
            if (!$registration) {
                Log::error('ServiceRegistration not found for document processing', [
                    'registration_id' => $this->registrationId
                ]);
                return;
            }

            // Kiểm tra file có tồn tại không
            if (!Storage::disk('public')->exists($this->filePath)) {
                Log::error('Document file not found', [
                    'registration_id' => $this->registrationId,
                    'file_path' => $this->filePath
                ]);
                return;
            }

            // Xử lý file (có thể thêm logic xử lý file ở đây)
            // Ví dụ: tạo thumbnail, convert format, scan virus, etc.
            
            Log::info('Document processed successfully', [
                'registration_id' => $this->registrationId,
                'file_path' => $this->filePath,
                'original_name' => $this->originalName
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing document upload', [
                'registration_id' => $this->registrationId,
                'error' => $e->getMessage(),
                'file_path' => $this->filePath
            ]);

            // Có thể gửi email thông báo lỗi cho admin
            // Mail::to(config('admin.email'))->send(new DocumentProcessingErrorMail($this->registrationId, $e->getMessage()));
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Document upload job failed', [
            'registration_id' => $this->registrationId,
            'error' => $exception->getMessage(),
            'file_path' => $this->filePath
        ]);
    }
}
