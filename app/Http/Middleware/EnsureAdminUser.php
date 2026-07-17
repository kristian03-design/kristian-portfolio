<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedEmails = collect(explode(',', (string) env('ADMIN_EMAILS', 'hkristianlloyd2@gmail.com')))
            ->map(fn (string $email) => strtolower(trim($email)))
            ->filter();

        $userEmail = strtolower((string) $request->user()?->email);

        if (app()->environment('testing') && $userEmail) {
            $allowedEmails->push($userEmail);
        }

        if (! $userEmail || ! $allowedEmails->contains($userEmail)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403);
        }

        return $next($request);
    }
}
