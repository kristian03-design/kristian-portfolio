<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Access | Kristian Hernandez</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/chibi-logo.png') }}">

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/auth-login.css', 'resources/js/auth-login.js'])
</head>
<body>
<main class="auth-shell">
  <div class="ambient ambient-one"></div>
  <div class="ambient ambient-two"></div>
  <div class="ambient ambient-three"></div>

  <section class="auth-copy">
    <a href="/" class="brand-lock">
      <span class="brand-mark"><i data-lucide="shield-check" class="lucide-icon"></i></span>
      <span>
        <span class="brand-name">Kristian H.</span>
        <span class="brand-sub">Portfolio CMS</span>
      </span>
    </a>

    <div class="copy-stack">
      <span class="copy-kicker">Secure Admin Workspace</span>
      <h1>Control the portfolio with precision.</h1>
      <p>Projects, skills, timelines, and messages live behind a focused authentication flow with OTP verification.</p>
    </div>

    <a href="/" class="return-link">
      <i data-lucide="arrow-left" class="lucide-icon"></i>
      Back to Portfolio
    </a>
  </section>

  <section class="auth-panel">
    <div class="form-card">
      <div class="card-glow"></div>
      <div class="form-eyebrow">Secure Access</div>
      <h2 class="form-title">Admin Login</h2>
      <p class="form-subtitle">Enter your credentials to continue.</p>

      @if (session('status'))
        <div class="auth-alert success">{{ session('status') }}</div>
      @endif

      @if ($errors->any())
        <div class="auth-alert error">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <div class="input-wrap">
            <span class="input-icon"><i data-lucide="mail" class="lucide-icon"></i></span>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="admin@example.com" required autofocus autocomplete="username">
          </div>
        </div>

        <div class="form-group">
          <div class="label-row">
            <label class="form-label form-label-tight" for="password">Password</label>
            <a href="#" class="forgot-link">Forgot Password?</a>
          </div>
          <div class="input-wrap">
            <span class="input-icon"><i data-lucide="lock" class="lucide-icon"></i></span>
            <input id="password" type="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
          </div>
        </div>

        <div class="remember-row">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Keep me signed in</label>
        </div>

        <button type="submit" class="btn-submit">
          Sign In
          <i data-lucide="arrow-right" class="lucide-icon"></i>
        </button>
      </form>
    </div>
  </section>
</main>
</body>
</html>
