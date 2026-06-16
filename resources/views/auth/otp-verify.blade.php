<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Verify Access | Kristian Hernandez</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/chibi-logo.png') }}">

@vite(['resources/css/auth-otp.css', 'resources/js/auth-otp.js'])
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

    <div class="verify-steps">
      <span class="copy-kicker">Two-step Security</span>
      <h1>One last checkpoint.</h1>
      <div class="step done">
        <div class="step-num"><i data-lucide="check" class="lucide-icon"></i></div>
        <div>
          <div class="step-text">Credentials verified</div>
          <div class="step-sub">Email and password accepted</div>
        </div>
      </div>
      <div class="step active">
        <div class="step-num">2</div>
        <div>
          <div class="step-text">Enter your OTP code</div>
          <div class="step-sub">6-digit code sent to your email</div>
        </div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div>
          <div class="step-text">Access granted</div>
          <div class="step-sub">Redirected to admin dashboard</div>
        </div>
      </div>
    </div>

    <a href="/login" class="return-link">
      <i data-lucide="arrow-left" class="lucide-icon"></i>
      Back to Login
    </a>
  </section>

  <section class="auth-panel">
    <div class="otp-card">
      <div class="card-glow"></div>
      <div class="otp-icon-wrap"><i data-lucide="shield-check" class="otp-main-icon"></i></div>
      <h2 class="otp-title">Security Check</h2>
      <p class="otp-subtitle">We've sent a 6-digit code to your email. Enter it below to verify your identity.</p>

      @if (session('success'))
        <div class="auth-alert success">{{ session('success') }}</div>
      @endif

      @error('otp')
        <div class="auth-alert error">{{ $message }}</div>
      @enderror

      <form id="otpForm" method="POST" action="{{ route('otp.verify') }}">
        @csrf
        <input type="hidden" name="otp" id="otp">
        <div class="otp-boxes" id="otpBoxes">
          <input class="otp-box" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="one-time-code" aria-label="Digit 1">
          <input class="otp-box" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="Digit 2">
          <input class="otp-box" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="Digit 3">
          <input class="otp-box" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="Digit 4">
          <input class="otp-box" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="Digit 5">
          <input class="otp-box" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="Digit 6">
        </div>

        <div class="timer-row">
          <span>Expires in</span>
          <span class="timer-val" id="countdown">10:00</span>
        </div>

        <button type="submit" class="btn-verify" id="submitBtn" disabled>
          Verify &amp; Continue
          <i data-lucide="arrow-right" class="lucide-icon"></i>
        </button>
      </form>

      <a href="{{ route('otp.resend') }}" class="resend-btn" id="resendBtn">Resend Code</a>

      <div class="sep"></div>
      <a href="/login" class="back-link"><i data-lucide="arrow-left" class="lucide-icon"></i>Back to Login</a>
    </div>
  </section>
</main>
</body>
</html>
