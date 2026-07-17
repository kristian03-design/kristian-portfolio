<section id="tab-profile" class="tab">
    <div class="two-col">
        
        <!-- Profile Details Form -->
        <form method="POST" action="{{ route('profile.update') }}" class="panel">
            @csrf
            @method('PATCH')
            <div class="panel-head">
                <div class="panel-title">
                    <i class="ti ti-user-edit"></i> Profile Information
                </div>
            </div>
            <div class="form-body">
                <div class="field-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="field" value="{{ auth()->user()->full_name }}" required>
                </div>
                <div class="field-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="field" value="{{ auth()->user()->email }}" required>
                </div>
                <div class="field-group">
                    <label>Position / Role</label>
                    <input type="text" name="position" class="field" value="{{ auth()->user()->position }}">
                </div>
                <button type="submit" class="btn btn-primary full-width">
                    <i class="ti ti-device-floppy"></i> Save Profile Details
                </button>
            </div>
        </form>

        <!-- Security / Password Column -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- Change Password Form -->
            <form method="POST" action="{{ route('password.update') }}" class="panel" style="margin-bottom: 0;">
                @csrf
                @method('PUT')
                <div class="panel-head">
                    <div class="panel-title">
                        <i class="ti ti-key"></i> Update Password
                    </div>
                </div>
                <div class="form-body">
                    <div class="field-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="field" required autocomplete="current-password">
                    </div>
                    <div class="field-group">
                        <label>New Password</label>
                        <input type="password" name="password" class="field" required autocomplete="new-password">
                    </div>
                    <div class="field-group">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="field" required autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn btn-primary full-width">
                        <i class="ti ti-lock-open"></i> Change Password
                    </button>
                </div>
            </form>

            <!-- Two-Factor Authentication Switch -->
            <div class="panel" style="margin-bottom: 0;">
                <div class="panel-head">
                    <div class="panel-title">
                        <i class="ti ti-shield-lock"></i> Two-Factor Authentication
                    </div>
                </div>
                <div class="form-body">
                    <p style="font-size: 12.5px; line-height: 1.6; color: var(--text-secondary); margin-bottom: 20px;">
                        Secure your admin dashboard by enforcing email-based One-Time Passwords (OTP) upon every sign-in. This helps protect your technical portfolio from unauthorized modifications.
                    </p>
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="full_name" value="{{ auth()->user()->full_name }}">
                        <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                        <input type="hidden" name="position" value="{{ auth()->user()->position }}">

                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--bg-base); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <div>
                                <div style="font-size: 13.5px; font-weight: 700; color: var(--text-primary);">Enable Two-Factor Authentication</div>
                                <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">Require OTP verification at login</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="two_factor_enabled" value="1" onchange="this.form.submit()" {{ auth()->user()->two_factor_enabled ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Session Management Panel -->
    <div class="panel" style="margin-top: 24px;">
        <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="panel-title">
                <i class="ti ti-devices"></i> Active Login Sessions
            </div>
            @if($sessions->count() > 1)
                <form method="POST" action="{{ route('profile.sessions.terminate_others') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to terminate all other active login sessions?">
                        <i class="ti ti-logout"></i> Sign Out Other Devices
                    </button>
                </form>
            @endif
        </div>
        <div class="form-body" style="padding: 10px;">
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($sessions as $session)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--bg-base); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="font-size: 24px; color: var(--primary);">
                                @if($session->device === 'Mobile')
                                    <i class="ti ti-device-mobile"></i>
                                @elseif($session->device === 'Tablet')
                                    <i class="ti ti-device-tablet"></i>
                                @else
                                    <i class="ti ti-device-laptop"></i>
                                @endif
                            </div>
                            <div>
                                <div style="font-size: 13.5px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                                    {{ $session->browser }} on {{ $session->operating_system }}
                                    @if($session->is_current)
                                        <span class="badge badge-published" style="font-size: 8px; padding: 2px 6px;">Active Device</span>
                                    @endif
                                </div>
                                <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                                    IP: <strong>{{ $session->ip_address }}</strong> &bull; Last Activity: {{ $session->last_activity->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @if(!$session->is_current)
                            <form method="POST" action="{{ route('profile.sessions.terminate', $session->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--danger); border-color: rgba(224, 49, 49, 0.15);" data-confirm="Are you sure you want to terminate this remote session?">
                                    Revoke
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Login Activity & System Audit Logs Grid -->
    <div class="two-col" style="margin-top: 24px;">
        
        <!-- Login History -->
        <div class="panel" style="margin-bottom: 0;">
            <div class="panel-head">
                <div class="panel-title">
                    <i class="ti ti-history"></i> Login History (Last 15)
                </div>
            </div>
            <div class="form-body" style="padding: 12px; max-height: 400px; overflow-y: auto;">
                <div class="activity-list">
                    @forelse($loginHistory as $log)
                        <div class="activity-row">
                            <div>
                                <div style="font-weight: 600; color: var(--text-primary);">
                                    {{ ucfirst($log->type) }} Attempt
                                </div>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                    IP: {{ $log->ip_address }} &bull; {{ $log->browser }} ({{ $log->operating_system }})
                                </div>
                                @if(!$log->success && $log->failure_reason)
                                    <div style="font-size: 11px; color: var(--danger); margin-top: 2px;">
                                        Reason: {{ $log->failure_reason }}
                                    </div>
                                @endif
                            </div>
                            <span class="activity-badge {{ $log->success ? 'success' : 'failed' }}">
                                {{ $log->success ? 'Success' : 'Failed' }}
                            </span>
                        </div>
                    @empty
                        <div class="empty-panel" style="padding: 20px;">No login activities logged.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- System Audit Logs -->
        <div class="panel" style="margin-bottom: 0;">
            <div class="panel-head">
                <div class="panel-title">
                    <i class="ti ti-list-check"></i> System Audit Logs (Last 50)
                </div>
            </div>
            <div class="form-body" style="padding: 12px; max-height: 400px; overflow-y: auto;">
                <div class="activity-list">
                    @forelse($auditLogs as $log)
                        <div class="activity-row" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                            <div style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                                <strong style="color: var(--text-primary);">{{ str_replace('_', ' ', strtoupper($log->action)) }}</strong>
                                <span style="font-size: 11px; color: var(--text-muted);">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <div style="font-size: 11.5px; color: var(--text-secondary);">
                                By: <strong>{{ $log->user->full_name ?? 'System' }}</strong> &bull; IP: {{ $log->ip_address }}
                            </div>
                            @if($log->changes)
                                <div style="font-family: var(--font-mono); font-size: 10px; background: rgba(0,0,0,0.02); padding: 4px 8px; border-radius: 4px; border: 1px solid var(--border-color); width: 100%; margin-top: 4px; overflow-x: auto;">
                                    {!! json_encode($log->changes) !!}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="empty-panel" style="padding: 20px;">No audits recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</section>
