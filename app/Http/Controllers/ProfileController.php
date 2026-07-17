<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): RedirectResponse
    {
        return redirect()->route('admin.dashboard', ['tab' => 'profile']);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        
        $validated['two_factor_enabled'] = $request->has('two_factor_enabled');

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Log audit event
        \App\Services\AuditLogger::log('profile_update', 'adminlist', $user->id, [
            'full_name' => $user->full_name,
            'email' => $user->email,
            'position' => $user->position,
            'two_factor_enabled' => $user->two_factor_enabled,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'profile'])->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Log audit event before logging out
        \App\Services\AuditLogger::log('profile_delete', 'adminlist', $user->id);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Terminate a specific active session.
     */
    public function terminateSession(Request $request, string $sessionId): RedirectResponse
    {
        $userId = Auth::id();

        \Illuminate\Support\Facades\DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', $userId)
            ->delete();

        // Log audit event
        \App\Services\AuditLogger::log('session_terminate', 'sessions', null, ['session_id' => $sessionId]);

        return redirect()->route('admin.dashboard', ['tab' => 'profile'])->with('success', 'Session terminated successfully.');
    }

    /**
     * Terminate all other active sessions for the user.
     */
    public function terminateOtherSessions(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $currentSessionId = $request->session()->getId();

        \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        // Log audit event
        \App\Services\AuditLogger::log('session_terminate_all_others');

        return redirect()->route('admin.dashboard', ['tab' => 'profile'])->with('success', 'All other active sessions terminated.');
    }
}
