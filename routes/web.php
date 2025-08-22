<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register');


Route::middleware('auth')->group(function () {
    Route::prefix('~admin')->name('admin.')->group(function () {
        Route::get('profile', [UserProfileController::class, 'index'])->name('profile');
        Route::put('profile/update', [UserProfileController::class, 'update'])->name('profile.update');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    Route::prefix('user')->middleware('auth')->group(function () {
        Route::get('profile', [UserProfileController::class, 'index'])->name('profile');
    });

    Route::get('/logout', LogoutController::class)->name('logout');
});
