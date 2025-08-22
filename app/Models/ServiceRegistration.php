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
        'department_id',
        'queue_number',
        'status',
        'notes'
    ];

    protected $casts = [
        'birth_year' => 'integer',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Đã xử lý',
            'cancelled' => 'Đã hủy'
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}
