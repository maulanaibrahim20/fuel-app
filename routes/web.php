<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register');


Route::prefix('~admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.admin.dashboard.index');
    });

    Route::get('/logout', LogoutController::class)->name('logout');
});

Route::prefix('user')->middleware('auth')->group(function () {});
