<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceApiController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes (không cần authentication)
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/auth/login', [AuthController::class, 'login']);
    
    // Public service routes (có thể truy cập mà không cần auth)
    Route::get('/departments', [ServiceApiController::class, 'getDepartments']);
    Route::get('/queue-status', [ServiceApiController::class, 'getQueueStatus']);
});

// Protected routes (cần authentication)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Authentication management
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    
    // Service registration (cần auth để đăng ký)
    Route::post('/register', [ServiceApiController::class, 'register']);
    
    // Queue management (cần auth để kiểm tra)
    Route::post('/check-queue', [ServiceApiController::class, 'checkQueueNumber']);
    
    // Statistics (cần auth để xem thống kê)
    Route::get('/statistics', [ServiceApiController::class, 'getStatistics']);
});

// Legacy route for backward compatibility
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
