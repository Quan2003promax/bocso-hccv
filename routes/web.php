<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
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
Route::post('/service-register', [HomeController::class, 'register'])->name('service.register');

// Test route
Route::get('/test-form', function () {
    return view('test-form');
});

// Test API route
Route::get('/api-test', function () {
    return view('api-test');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->hasRole('Super-Admin')) {
        return redirect()->route('users.index');
    }

    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::resource('users', UserController::class);
// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('departments', DepartmentController::class);
    Route::resource('service-registrations', ServiceRegistrationController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('service-registrations/{registration}/status', [ServiceRegistrationController::class, 'updateStatus'])->name('service-registrations.update-status');
    Route::delete('/registrations/{id}', [ServiceRegistrationController::class, 'destroy'])
    ->name('registrations.destroy');

});
require __DIR__ . '/auth.php';
