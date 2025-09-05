<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ServiceRegistration;
use Symfony\Component\HttpFoundation\Response;

class CheckServiceRegistrationDepartment
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Bạn chưa đăng nhập.');
        }

        // Lấy ID hồ sơ từ route (tên param có thể là id hoặc service_registration tuỳ resource)
        $registrationId = $request->route('service_registration') ?? $request->route('id');

        if ($registrationId) {
            $registration = ServiceRegistration::find($registrationId);

            if (!$registration) {
                abort(404, 'Không tìm thấy hồ sơ.');
            }

            // Kiểm tra user có quản lý phòng ban của hồ sơ không
            if (!$user->departments->contains('id', $registration->department_id)) {
                abort(403, 'Bạn không có quyền với hồ sơ thuộc phòng ban này.');
            }
        }

        return $next($request);
    }
}
