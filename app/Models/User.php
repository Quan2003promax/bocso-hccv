<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the department that owns the user.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Check if user has permission in specific department
     */
    public function hasPermissionInDepartment($permission, $departmentName = null)
    {
        // Nếu không yêu cầu phòng ban cụ thể, chỉ kiểm tra quyền
        if (!$departmentName) {
            return $this->hasPermissionTo($permission);
        }

        // Kiểm tra cả quyền và phòng ban
        return $this->hasPermissionTo($permission) && 
               $this->department && 
               $this->department->name === $departmentName;
    }

    /**
     * Check if user belongs to specific department
     */
    public function belongsToDepartment($departmentName)
    {
        return $this->department && $this->department->name === $departmentName;
    }
}
