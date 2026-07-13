<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Resume Temporarily Unavailable &mdash; Kristian Hernandez</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/chibi-logo.png') }}">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:wght@400;500;600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  @vite(['resources/css/portfolio.css'])
  <script>
    (function() {
      const savedTheme = localStorage.getItem('theme');
      const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', theme);
      document.documentElement.classList.remove('dark', 'light');
      document.documentElement.classList.add(theme);
    })();
  </script>
</head>
<body style="display: grid; place-items: center; min-height: 100vh; padding: 2rem;">
  <div class="welcome-card" style="text-align: center; max-width: 480px; position: relative;">
    <div class="welcome-kicker" style="color: var(--lime); justify-content: center;">Notice</div>
    <h1 class="welcome-title" style="font-size: 2.75rem; margin-top: 0.5rem; margin-bottom: 1.5rem;">TEMPORARILY<br>UNAVAILABLE.</h1>
    <p class="welcome-copy" style="margin-bottom: 2rem;">
      The resume is temporarily unavailable. Please contact me directly at <strong>hkristianlloyd2@gmail.com</strong> for a copy.
    </p>
    <div class="welcome-actions" style="display: flex; justify-content: center; gap: 1rem;">
      <a href="{{ route('home') }}" class="welcome-primary" style="text-decoration: none; padding: .85rem 2rem; border-radius: 2px;">Back to Home</a>
    </div>
  </div>
</body>
</html>
