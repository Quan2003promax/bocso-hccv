<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;

class CheckDepartmentPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @param  string  $department
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = null, $department = null)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Kiểm tra quyền nếu có yêu cầu
        if ($permission && !$user->hasPermissionTo($permission)) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập chức năng này!');
        }

        // Kiểm tra phòng ban nếu có yêu cầu
        if ($department) {
            $userDepartment = $user->department;
            if (!$userDepartment || $userDepartment->name !== $department) {
                return redirect()->back()->with('error', 'Bạn không thuộc phòng ban được yêu cầu!');
            }
        }

        return $next($request);
    }
}
