<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status'
    ];

    public function serviceRegistrations()
    {
        return $this->hasMany(ServiceRegistration::class);
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_department', 'department_id', 'user_id')
                    ->withTimestamps();
    }
}
