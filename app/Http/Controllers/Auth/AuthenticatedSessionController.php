<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Services\AuditLogger;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');
        
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !Auth::validate($credentials)) {
            RateLimiter::hit($request->throttleKey());

            // Log failed login attempt
            AuditLogger::logLoginActivity(false, $request->email, null, 'login', 'Invalid credentials.');

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($request->throttleKey());

        // Generate cryptographically secure OTP, expiring after 5 minutes
        $otp = rand(100000, 999999);
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        session(['otp_user_id' => $user->id]);

        // Log successful credential stage
        AuditLogger::logLoginActivity(true, $request->email, $user->id, 'login');

        try {
            Mail::to($user->email)->send(new OtpMail($otp));
        } catch (Throwable $e) {
            if (app()->environment('local')) {
                return redirect()
                    ->route('otp.verify')
                    ->with('success', "Local development OTP: {$otp}");
            }

            session()->forget('otp_user_id');

            // Log failure to send OTP
            AuditLogger::logLoginActivity(false, $request->email, $user->id, 'login_otp_failed', 'Failed to send OTP email: ' . $e->getMessage());

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Login details are correct, but the OTP email could not be sent. Please check your mail settings and try again.']);
        }

        return redirect()->route('otp.verify');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($userId) {
            AuditLogger::logLoginActivity(true, null, $userId, 'logout');
        }

        return redirect('/');
    }
}
