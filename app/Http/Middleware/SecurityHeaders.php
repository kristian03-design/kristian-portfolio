<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->remove('X-Powered-By');

        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
               "img-src 'self' data: https://cdn.jsdelivr.net https://cdn.simpleicons.org; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "connect-src 'self'; " .
               "frame-ancestors 'none'; " .
               "form-action 'self';";
        $response->headers->set('Content-Security-Policy', $csp);

        if ($request->is('admin*', 'dashboard*', 'profile*', 'login', 'register', 'otp*', 'forgot-password', 'reset-password*', 'media*')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        }

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
