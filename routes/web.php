<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\Backend\DepartmentController;
use App\Http\Controllers\Backend\ServiceRegistrationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Trang chủ cho người dùng
Route::get('/', [HomeController::class, 'index'])->name('home');
// Ajax search by queue number (Số thứ tự)
Route::get('/search/queue', [HomeController::class, 'search'])->name('search.queue');
Route::post('/service-register', [HomeController::class, 'register'])
    ->name('service.register')
    ->middleware('throttle.service.registration');



Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->hasRole('Super-Admin')) {
        return redirect()->route('users.index');
    }

    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    
    // Routes demo middleware mới
    Route::prefix('example')->name('example.')->group(function () {
        Route::get('/', [ExampleController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [ExampleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ExampleController::class, 'update'])->name('update');
        Route::delete('/{id}', [ExampleController::class, 'destroy'])->name('destroy');
        Route::get('/hr-only', [ExampleController::class, 'hrOnly'])->name('hr-only');
        Route::get('/check-permissions', [ExampleController::class, 'checkPermissions'])->name('check-permissions');
    });
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('departments', DepartmentController::class);
    Route::resource('service-registrations', ServiceRegistrationController::class)->except(['edit', 'update']);
    Route::post('service-registrations', [ServiceRegistrationController::class, 'store'])->name('service-registrations.store');
    Route::patch('service-registrations/{id}/status', [ServiceRegistrationController::class, 'updateStatus'])
    ->name('service-registrations.update-status');
    Route::delete('/registrations/{id}', [ServiceRegistrationController::class, 'destroy'])
    ->name('registrations.destroy');
});

// routes/web.php
Route::get('/test-status', function () {
    \App\Events\StatusUpdated::dispatch([
        'status' => 'online',
        'message' => 'Test ok',
        'at' => now()->toDateTimeString(),
    ]);
    return 'Broadcasted';
});

require __DIR__ . '/auth.php';
