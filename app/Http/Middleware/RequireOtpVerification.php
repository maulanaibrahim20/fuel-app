<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireOtpVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        $allowedRoutes = [
            'send-otp',
            'verify-otp',
            'resent-otp',
            'logout'
        ];

        if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        $phoneVerified = !is_null($user->phone_verified_at);
        $emailVerified = !is_null($user->email_verified_at);

        // Jika belum ada yang diverifikasi sama sekali, redirect ke send-otp
        if (!$phoneVerified && !$emailVerified) {
            return redirect()->route('send-otp')->with('warning', 'Please verify your mobile phone number or email address first..');
        }

        // Jika sudah ada salah satu yang diverifikasi, lanjutkan
        return $next($request);
    }
}
