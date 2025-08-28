<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ThrottleServiceRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'service_registration:' . $request->ip();
        
        // Giới hạn: tối đa 5 đăng ký mỗi giờ từ cùng một IP
        // if (RateLimiter::tooManyAttempts($key, 5)) {
        //     $seconds = RateLimiter::availableIn($key);
            
        //     Log::warning('Rate limit exceeded for service registration', [
        //         'ip' => $request->ip(),
        //         'user_agent' => $request->userAgent(),
        //         'seconds_remaining' => $seconds
        //     ]);
            
        //     if ($request->expectsJson()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Bạn đã đăng ký quá nhiều lần. Vui lòng thử lại sau ' . ceil($seconds / 60) . ' phút.',
        //             'retry_after' => $seconds
        //         ], 429);
        //     }
            
        //     return redirect()->back()
        //         ->withErrors(['general' => 'Bạn đã đăng ký quá nhiều lần. Vui lòng thử lại sau ' . ceil($seconds / 60) . ' phút.'])
        //         ->withInput();
        // }
        
        RateLimiter::hit($key, 3600); // 1 giờ
        
        $response = $next($request);
        
        // Nếu đăng ký thành công, tăng rate limit
        if ($response->getStatusCode() === 302 && session('success')) {
            RateLimiter::hit($key, 3600);
        }
        
        return $response;
    }
}
