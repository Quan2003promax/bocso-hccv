<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'birth_year',
        'identity_number',
        'email',
        'phone',
        'document_file',
        'document_original_name',
        'document_mime_type',
        'document_size',
        'department_id',
        'queue_number',
        'status',
        'notes'
    ];

    protected $casts = [
        'birth_year' => 'integer',
        'document_size' => 'integer',
    ];

    // Validation rules
    public static function getRules()
    {
        return [
            'full_name' => 'required|string|max:255',
            'birth_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'identity_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15|regex:/^[0-9+\-\s\(\)]+$/',
            'department_id' => 'required|exists:departments,id',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:131072', // 128MB = 131072 KB
        ];
    }

    // Custom validation messages
    public static $messages = [
        'full_name.required' => 'Vui lòng nhập họ và tên',
        'full_name.max' => 'Họ và tên không được quá 255 ký tự',
        'birth_year.required' => 'Vui lòng nhập năm sinh',
        'birth_year.integer' => 'Năm sinh phải là số nguyên',
        'birth_year.min' => 'Năm sinh không hợp lệ',
        'birth_year.max' => 'Năm sinh không hợp lệ',
        'identity_number.required' => 'Vui lòng nhập số căn cước công dân',
        'identity_number.max' => 'Số căn cước công dân không được quá 20 ký tự',
        'email.required' => 'Vui lòng nhập email',
        'email.email' => 'Email không đúng định dạng',
        'email.max' => 'Email không được quá 255 ký tự',
        'phone.required' => 'Vui lòng nhập số điện thoại',
        'phone.max' => 'Số điện thoại không được quá 15 ký tự',
        'phone.regex' => 'Số điện thoại không đúng định dạng',
        'department_id.required' => 'Vui lòng chọn phòng ban',
        'department_id.exists' => 'Phòng ban không tồn tại',
        'document_file.file' => 'File không hợp lệ',
        'document_file.mimes' => 'Chỉ hỗ trợ file: PDF, DOC, DOCX, JPG, PNG, GIF',
        'document_file.max' => 'Kích thước file không được quá 128MB',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'received' => 'Đã tiếp nhận',
            'processing' => 'Đang xử lý',
            'completed' => 'Đã xử lý',
            'cancelled' => 'Đã hủy',
            'returned' => 'Trả hồ sơ'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    // Helper method để format kích thước file
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->document_size) return null;
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->document_size;
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }
}
