<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Portfolio CMS</title>
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
                <span style="font-size: 11px; font-weight: 700; color: #a4be35; text-transform: uppercase; letter-spacing: 1.5px; display: inline-block; margin-bottom: 12px; background: rgba(164, 190, 53, 0.1); padding: 4px 10px; border-radius: 20px;">Secure Management Portal</span>
                <h1 class="brand-heading">Control your portfolio with absolute precision.</h1>
                <p class="brand-subtitle">Manage projects, review OCR verified certifications, organize learning paths, and coordinate guest communications from a centralized, secure dashboard.</p>
            </div>

            <div class="brand-security-statement">
                <i class="ti ti-shield-lock"></i>
                <div>
                    <strong>Enterprise DevSecOps Protocol Active</strong>
                    <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">Your session IP is monitored: <strong>{{ request()->ip() }}</strong></div>
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="auth-form-side">
            <div class="auth-form-card">
                <div class="form-header">
                    <h2 class="form-title">Welcome back</h2>
                    <p class="form-subtitle">Enter your details to log in to your admin workspace.</p>
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

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="ti ti-mail input-icon-left"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" class="auth-input" placeholder="admin@example.com" required autofocus autocomplete="username">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div class="form-label-row">
                            <label class="form-label" for="password">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="form-link">Forgot password?</a>
                            @endif
                        </div>
                        <div class="input-wrapper">
                            <i class="ti ti-lock input-icon-left"></i>
                            <input id="password" type="password" name="password" class="auth-input" placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" class="btn-toggle-password" onclick="togglePass()">
                                <i class="ti ti-eye" id="password-toggle-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember me -->
                    <div class="checkbox-row">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" id="remember_me">
                            <span>Keep me signed in</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-primary" style="margin-top: 10px;">
                        Sign In <i class="ti ti-arrow-right"></i>
                    </button>
                </form>

                <div style="margin-top: 32px; text-align: center;">
                    <a href="/" class="form-link" style="display: inline-flex; align-items: center; gap: 4px; font-size: 13px;">
                        <i class="ti ti-arrow-left"></i> Return to Portfolio Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePass() {
            const input = document.getElementById('password');
            const icon = document.getElementById('password-toggle-icon');
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
