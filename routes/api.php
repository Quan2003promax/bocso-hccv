<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes cho hệ thống bốc số thứ tự
Route::prefix('v1')->group(function () {
    // Đăng ký dịch vụ
    Route::post('/register', [ServiceApiController::class, 'register']);
    
    // Lấy danh sách phòng ban
    Route::get('/departments', [ServiceApiController::class, 'getDepartments']);
    
    // Lấy trạng thái hàng đợi
    Route::get('/queue-status', [ServiceApiController::class, 'getQueueStatus']);
    
    // Kiểm tra số thứ tự
    Route::post('/check-queue', [ServiceApiController::class, 'checkQueueNumber']);
    
    // Thống kê tổng quan
    Route::get('/statistics', [ServiceApiController::class, 'getStatistics']);
});
