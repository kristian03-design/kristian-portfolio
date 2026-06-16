<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Throwable;

class OtpController extends Controller
{
    public function show()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.otp-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail(session('otp_user_id'));

        if ($user->otp_code == $request->otp && now()->lt($user->otp_expires_at)) {
            // Clear OTP
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);

            // Log user in
            Auth::login($user);

            session()->forget('otp_user_id');
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        }

        throw ValidationException::withMessages([
            'otp' => 'The provided OTP is invalid or has expired.',
        ]);
    }

    public function resend()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail(session('otp_user_id'));
        
        $otp = rand(100000, 999999);
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($user->email)->send(new OtpMail($otp));
        } catch (Throwable) {
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
