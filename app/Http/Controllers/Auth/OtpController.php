<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Services\AuditLogger;
use Throwable;

class OtpController extends Controller
{
    public function show(Request $request)
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail(session('otp_user_id'));
        $uaParsed = AuditLogger::parseUserAgent($request->userAgent());
        $ip = $request->ip();

        return view('auth.otp-verify', compact('user', 'uaParsed', 'ip'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'remember_device' => 'nullable|boolean',
        ]);

        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail(session('otp_user_id'));
        $attemptsKey = 'otp_attempts:' . $user->id;
        $attempts = cache()->get($attemptsKey, 0);

        if ($attempts >= 5) {
            AuditLogger::logLoginActivity(false, $user->email, $user->id, 'otp_verify', 'Too many attempts. Lockout.');
            throw ValidationException::withMessages([
                'otp' => ['Too many failed attempts. Please request a new OTP.'],
            ]);
        }

        if ($user->otp_code == $request->otp && now()->lt($user->otp_expires_at)) {
            // Clear OTP
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);

            cache()->forget($attemptsKey);

            // Log user in
            Auth::login($user);

            session()->forget('otp_user_id');
            $request->session()->regenerate();

            // Set trusted device cookie for 30 days if checked
            if ($request->boolean('remember_device')) {
                $hash = hash_hmac('sha256', $request->ip() . '|' . $request->userAgent(), config('app.key'));
                cookie()->queue('trusted_device_' . $user->id, $hash, 30 * 24 * 60); // 30 days
            }

            // Log successful sign-in
            AuditLogger::logLoginActivity(true, $user->email, $user->id, 'otp_verify');

            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Increment attempts on failure
        $attempts = cache()->increment($attemptsKey, 1);
        if ($attempts === 1) {
            cache()->put($attemptsKey, 1, now()->addMinutes(5));
        }

        if ($attempts >= 5) {
            // Invalidate the code immediately
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);
            AuditLogger::logLoginActivity(false, $user->email, $user->id, 'otp_verify', 'Locked out due to max OTP attempts.');
            throw ValidationException::withMessages([
                'otp' => ['Too many failed attempts. This OTP has been invalidated.'],
            ]);
        }

        AuditLogger::logLoginActivity(false, $user->email, $user->id, 'otp_verify', 'Incorrect OTP entered.');

        throw ValidationException::withMessages([
            'otp' => ['The provided OTP is invalid or has expired.'],
        ]);
    }

    public function resend()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail(session('otp_user_id'));
        
        // Reset attempts key on resend so they get a fresh start
        cache()->forget('otp_attempts:' . $user->id);

        $otp = rand(100000, 999999);
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5), // 5 min expiry
        ]);

        try {
            Mail::to($user->email)->send(new OtpMail($otp));
        } catch (Throwable $e) {
            if (app()->environment('local')) {
                return redirect()->back()->with('success', "Local development OTP: {$otp}");
            }

            return redirect()->back()->withErrors([
                'otp' => 'A new OTP could not be sent. Please check your mail settings and try again.',
            ]);
        }

        return redirect()->back()->with('success', 'A new OTP has been sent to your email.');
    }
}
