<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Admin Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/chibi-logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth-premium.css') }}">
</head>
<body>
    <div class="auth-split-container">
        <!-- Left Side: Branding/Welcome -->
        <div class="auth-brand-side">
            <div class="brand-logo-container">
                <img src="{{ asset('images/chibi-logo.png') }}" alt="Logo" class="brand-logo">
                <span class="brand-title">Kristian<span>Hernandez</span></span>
            </div>
            
            <div class="brand-hero-content">
                <h1 class="brand-heading">Reset Password</h1>
                <p class="brand-subtitle">Set a secure, high-entropy password to protect your admin dashboard, upload pathways, and application database details.</p>
            </div>

            <div class="brand-security-statement">
                <i class="ti ti-shield-check"></i>
                <div>
                    <strong>Secure Link</strong>
                    <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">This reset token is cryptographically verified.</div>
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="auth-form-side">
            <div class="auth-form-card">
                <div class="form-header">
                    <h2 class="form-title">New Password</h2>
                    <p class="form-subtitle">Choose a strong password for your account.</p>
                </div>

                @if ($errors->any())
                    <div class="alert-feedback alert-error">
                        <i class="ti ti-alert-triangle" style="font-size: 18px;"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" class="auth-form">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="ti ti-mail input-icon-left"></i>
                            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" class="auth-input" required autofocus autocomplete="username">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="ti ti-lock input-icon-left"></i>
                            <input id="password" type="password" name="password" class="auth-input" placeholder="••••••••" required autocomplete="new-password">
                            <button type="button" class="btn-toggle-password" onclick="togglePass('password')">
                                <i class="ti ti-eye" id="password-toggle-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="ti ti-lock-open input-icon-left"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="auth-input" placeholder="••••••••" required autocomplete="new-password">
                            <button type="button" class="btn-toggle-password" onclick="togglePass('password_confirmation')">
                                <i class="ti ti-eye" id="password_confirmation-toggle-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-primary" style="margin-top: 10px;">
                        <i class="ti ti-key"></i> Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePass(id) {
            const input = document.getElementById(id);
            const icon = document.getElementById(id + '-toggle-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ti-eye', 'ti-eye-off');
            } else {
                input.type = 'password';
                icon.classList.replace('ti-eye-off', 'ti-eye');
            }
        }
    </script>
</body>
</html>
