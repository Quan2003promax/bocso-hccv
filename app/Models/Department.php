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

    /**
     * Get the users for the department.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if department is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}
