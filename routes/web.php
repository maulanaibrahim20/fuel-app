<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login');

    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])->name('forgot-password');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('forgot-password');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/send-otp', [OtpController::class, 'index'])->name('send-otp');
        Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('send-otp');
        Route::get('/verify-otp', [OtpController::class, 'verifyOtp'])->name('verify-otp');
        Route::post('/verify-otp', [OtpController::class, 'validateOtp'])->name('verify-otp');
        Route::post('/resent-otp', [OtpController::class, 'resentOtp'])->name('resent-otp');
    });

    Route::middleware('require.otp')->group(function () {
        Route::prefix('~admin')->middleware('role:Super Admin')->name('admin.')->group(function () {
            Route::get('profile', [UserProfileController::class, 'index'])->name('profile');
            Route::put('profile/update', [UserProfileController::class, 'update'])->name('profile.update');

            Route::group(['prefix' => 'user', 'controller' => UserController::class], function () {
                Route::get('/', 'index')->name('user');
                Route::get('/getData', 'getData')->name('user.getData');
            });

            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        });

        Route::prefix('user')->middleware('role:User')->name('user.')->group(function () {
            Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
            Route::get('profile', [UserProfileController::class, 'index'])->name('profile');
        });
    });

    Route::get('/logout', LogoutController::class)->name('logout');
});
