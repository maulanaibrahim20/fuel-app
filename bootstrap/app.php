<?php

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RequireOtpVerification;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'require.otp' => RequireOtpVerification::class,
            'role' => RoleMiddleware::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            if (! $request->user()) {
                return '/login';
            }

            $user = Auth::user();

            if ($user->hasRole('Super Admin')) {
                return '/~admin/dashboard';
            } elseif ($user->hasRole('User')) {
                return '/user/dashboard';
            } else {
                return '/welcome';
            }
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
