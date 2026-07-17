<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Admin Portal</title>
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
                <h1 class="brand-heading">Security First. Always.</h1>
                <p class="brand-subtitle">Resetting your credentials requires multi-step authentication verification to protect content, database relationships, and private files.</p>
            </div>

            <div class="brand-security-statement">
                <i class="ti ti-shield-check"></i>
                <div>
                    <strong>Secure Session</strong>
                    <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">Your IP: {{ request()->ip() }}</div>
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="auth-form-side">
            <div class="auth-form-card">
                <div class="form-header">
                    <h2 class="form-title">Forgot Password?</h2>
                    <p class="form-subtitle">Enter your email address to receive a secure recovery link.</p>
                </div>

                @if (session('status'))
                    <div class="alert-feedback alert-success">
                        <i class="ti ti-circle-check" style="font-size: 18px;"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-feedback alert-error">
                        <i class="ti ti-alert-triangle" style="font-size: 18px;"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                    @csrf

                    <!-- Email Input -->
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="ti ti-mail input-icon-left"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" class="auth-input" placeholder="admin@example.com" required autofocus autocomplete="username">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-primary" style="margin-top: 10px;">
                        <i class="ti ti-mail-forward"></i> Send Password Reset Link
                    </button>
                </form>

                <div style="margin-top: 24px; text-align: center;">
                    <a href="{{ route('login') }}" class="form-link" style="display: inline-flex; align-items: center; gap: 4px;">
                        <i class="ti ti-arrow-left"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
