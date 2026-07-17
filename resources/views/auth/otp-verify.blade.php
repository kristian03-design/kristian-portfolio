<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | Portfolio CMS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/chibi-logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth-premium.css') }}">
</head>
<body>
    <div class="auth-split-container">
        <!-- Left Side: Branding/Steps -->
        <div class="auth-brand-side">
            <div class="brand-logo-container">
                <img src="{{ asset('images/chibi-logo.png') }}" alt="Logo" class="brand-logo">
                <span class="brand-title">Kristian<span>Hernandez</span></span>
            </div>
            
            <div class="brand-hero-content">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 50%; font-size: 12px; color: #a4be35;"><i class="ti ti-check"></i></span>
                        <div>
                            <div style="font-size: 14px; font-weight: 600; color: #ffffff;">Credentials Verified</div>
                            <div style="font-size: 12px; color: #a3a3a3;">Password verification complete.</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #a4be35; border-radius: 50%; font-size: 12px; color: #121508; font-weight: 700;">2</span>
                        <div>
                            <div style="font-size: 14px; font-weight: 600; color: #ffffff;">One-Time Password</div>
                            <div style="font-size: 12px; color: #a3a3a3;">Verify the 6-digit code sent to your mail.</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; opacity: 0.5;">
                        <span style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 50%; font-size: 12px;">3</span>
                        <div>
                            <div style="font-size: 14px; font-weight: 600;">Access Granted</div>
                            <div style="font-size: 12px;">Redirecting to workspace...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="brand-security-statement">
                <i class="ti ti-shield-check"></i>
                <div>
                    <strong>Verification Required</strong>
                    <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">Your account uses mandatory 2FA.</div>
                </div>
            </div>
        </div>

        <!-- Right Side: OTP Entry -->
        <div class="auth-form-side">
            <div class="auth-form-card">
                <div class="form-header">
                    <h2 class="form-title">Security verification</h2>
                    <p class="form-subtitle">We've sent a code to your registered email address.</p>
                </div>

                @if (session('success'))
                    <div class="alert-feedback alert-success">
                        <i class="ti ti-circle-check" style="font-size: 18px;"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-feedback alert-error">
                        <i class="ti ti-alert-triangle" style="font-size: 18px;"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <form id="otpForm" method="POST" action="{{ route('otp.verify') }}" class="auth-form">
                    @csrf
                    <!-- Hidden field to hold the compiled OTP -->
                    <input type="hidden" name="otp" id="otp">

                    <div class="otp-input-container">
                        <input class="otp-digit-input" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" aria-label="Digit 1" required>
                        <input class="otp-digit-input" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" aria-label="Digit 2" required>
                        <input class="otp-digit-input" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" aria-label="Digit 3" required>
                        <input class="otp-digit-input" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" aria-label="Digit 4" required>
                        <input class="otp-digit-input" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" aria-label="Digit 5" required>
                        <input class="otp-digit-input" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" aria-label="Digit 6" required>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; margin: 8px 0;">
                        <span style="color: var(--muted-color); display: flex; align-items: center; gap: 4px;">
                            <i class="ti ti-hourglass"></i> Code expires in: <strong id="countdown" style="color: var(--fg-base);">05:00</strong>
                        </span>
                    </div>

                    <div class="checkbox-row" style="margin-bottom: 8px;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember_device" id="remember_device" value="1">
                            <span>Trust this device for 30 days</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary" id="submitBtn" disabled>
                        Verify &amp; Enter <i class="ti ti-arrow-right"></i>
                    </button>
                </form>

                <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('otp.resend') }}" class="form-link" style="display: inline-flex; align-items: center; gap: 4px; font-size: 13px;">
                        <i class="ti ti-refresh"></i> Resend code
                    </a>
                    <a href="{{ route('login') }}" class="form-link" style="display: inline-flex; align-items: center; gap: 4px; font-size: 13px;">
                        Sign out
                    </a>
                </div>

                <!-- Device Information Card -->
                @if (isset($uaParsed) && isset($ip))
                    <div class="device-info-card">
                        <div class="device-info-header">
                            <i class="ti ti-device-laptop"></i> Signing Device Details
                        </div>
                        <div class="device-info-grid">
                            <div class="device-info-label">OS:</div>
                            <div class="device-info-val">{{ $uaParsed['os'] }}</div>
                            
                            <div class="device-info-label">Browser:</div>
                            <div class="device-info-val">{{ $uaParsed['browser'] }}</div>
                            
                            <div class="device-info-label">Device Type:</div>
                            <div class="device-info-val">{{ $uaParsed['device'] }}</div>
                            
                            <div class="device-info-label">IP Address:</div>
                            <div class="device-info-val">{{ $ip }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('.otp-digit-input');
            const hiddenOtp = document.getElementById('otp');
            const form = document.getElementById('otpForm');
            const submitBtn = document.getElementById('submitBtn');

            // Focus on first input on load
            inputs[0].focus();

            // Set up input triggers
            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const val = e.target.value;
                    if (val.length > 0) {
                        // Focus next
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    }
                    updateCompiledOtp();
                });

                // Backspace support
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && input.value === '') {
                        if (index > 0) {
                            inputs[index - 1].focus();
                        }
                    }
                });

                // Clipboard paste support
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const text = (e.clipboardData || window.clipboardData).getData('text').trim();
                    if (/^\d{6}$/.test(text)) {
                        for (let i = 0; i < inputs.length; i++) {
                            inputs[i].value = text[i];
                        }
                        updateCompiledOtp();
                        if (hiddenOtp.value.length === 6) {
                            form.submit();
                        }
                    }
                });
            });

            function updateCompiledOtp() {
                let code = '';
                inputs.forEach(input => {
                    code += input.value;
                });
                hiddenOtp.value = code;
                
                if (code.length === 6) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }

            form.addEventListener('submit', (e) => {
                if (hiddenOtp.value.length !== 6) {
                    e.preventDefault();
                }
            });

            // Countdown timer (5 minutes)
            let timeRemaining = 5 * 60; // 5 mins in seconds
            const countdownEl = document.getElementById('countdown');

            const timer = setInterval(() => {
                timeRemaining--;
                const mins = Math.floor(timeRemaining / 60);
                const secs = timeRemaining % 60;
                
                countdownEl.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;

                if (timeRemaining <= 0) {
                    clearInterval(timer);
                    inputs.forEach(input => input.disabled = true);
                    submitBtn.disabled = true;
                    countdownEl.textContent = "Expired";
                    countdownEl.style.color = "var(--error-color)";
                }
            }, 1000);
        });
    </script>
</body>
</html>
