<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <title>@yield('title', 'Kristian Hernandez — Web & Mobile Developer')</title>
  <meta name="description" content="@yield('meta_description', 'Full-stack web and mobile developer based in Bulacan, Philippines. Laravel, Flutter, clean architecture.')">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ request()->url() }}">
  <meta property="og:title" content="@yield('title', 'Kristian Hernandez — Web & Mobile Developer')">
  <meta property="og:description" content="@yield('meta_description', 'Full-stack web and mobile developer based in Bulacan, Philippines. Laravel, Flutter, clean architecture.')">
  <meta property="og:image" content="@yield('og_image', asset('images/chibi-logo-v2.png'))">

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="{{ request()->url() }}">
  <meta property="twitter:title" content="@yield('title', 'Kristian Hernandez — Web & Mobile Developer')">
  <meta property="twitter:description" content="@yield('meta_description', 'Full-stack web and mobile developer based in Bulacan, Philippines. Laravel, Flutter, clean architecture.')">
  <meta property="twitter:image" content="@yield('og_image', asset('images/chibi-logo-v2.png'))">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
  <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
  <link rel="dns-prefetch" href="https://cdn.simpleicons.org">
  
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/chibi-logo-v2.png') }}">

  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&family=Instrument+Serif:ital@0;1&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&family=Instrument+Serif:ital@0;1&display=swap" media="print" onload="this.media='all'">
  <noscript>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&family=Instrument+Serif:ital@0;1&display=swap">
  </noscript>

  @vite(['resources/css/portfolio.css', 'resources/js/portfolio.js'])
  @yield('styles')
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
<body class="@yield('body_class')">

<div id="progress"></div>

<!-- Mobile Menu Overlay -->
<div id="mobile-menu" role="dialog" aria-modal="true" aria-label="Mobile Navigation" aria-hidden="true">
  <div class="mm-header">
    <a href="{{ route('home') }}" class="mm-logo">Kristian<span class="nav-logo-dot">.</span></a>
    <div class="mm-header-actions">
      <button id="mobile-theme-toggle" class="theme-toggle-btn mobile-toggle" aria-label="Toggle theme">
        <span class="sun-wrapper"><i data-lucide="sun" class="sun-icon" style="width: 18px; height: 18px;"></i></span>
        <span class="moon-wrapper"><i data-lucide="moon" class="moon-icon" style="width: 18px; height: 18px;"></i></span>
      </button>
      <button id="mm-close" class="mm-close-btn" aria-label="Close navigation menu">
        <i data-lucide="x" style="width: 22px; height: 22px;"></i>
      </button>
    </div>
  </div>

  <div class="mm-scroll-container">
    <nav class="mm-nav" aria-label="Mobile Navigation Links">
      @yield('mobile_menu_links')
    </nav>
    
    <div class="mm-footer">
      <div class="nav-resume-wrapper">
        <a href="{{ route('resume.view') }}" target="_blank" rel="noopener noreferrer" class="nav-resume-premium btn-view-resume" aria-label="View Resume in a new tab">
          <span class="btn-icon"></span> <span class="btn-text">View Resume</span> <i data-lucide="arrow-up-right" style="width: 16px; height: 16px; display: inline-block; vertical-align: -1px; margin-left: 2.5px;"></i>
        </a>
        <a href="{{ route('resume.download') }}" class="nav-resume-premium btn-download-resume" title="Download Resume" aria-label="Download Resume PDF">
          <i data-lucide="download" style="width: 16px; height: 16px;"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Navigation Bar -->
@yield('navigation')

<!-- Main Content -->
<main id="main-content">
  @yield('content')
</main>

<!-- Footer -->
<x-footer />

<!-- Lightbox Modal for Certifications & Galleries -->
<div id="portfolio-lightbox" class="lightbox-modal" onclick="closeLightbox()" role="dialog" aria-modal="true" aria-label="Image Zoom">
  <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
  <img class="lightbox-content" id="lightbox-img" alt="Certificate Zoom">
  <div id="lightbox-caption" class="lightbox-caption"></div>
</div>

<script>
  function openLightbox(imgUrl, captionText) {
    const lightbox = document.getElementById('portfolio-lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    
    if (lightbox && lightboxImg) {
      lightboxImg.src = imgUrl;
      if (lightboxCaption) {
        lightboxCaption.innerHTML = captionText;
      }
      lightbox.style.display = 'block';
      document.body.style.overflow = 'hidden'; // Disable page scrolling
    }
  }

  function closeLightbox() {
    const lightbox = document.getElementById('portfolio-lightbox');
    if (lightbox) {
      lightbox.style.display = 'none';
      document.body.style.overflow = ''; // Re-enable page scrolling
    }
  }

  // Close lightbox on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeLightbox();
    }
  });
</script>
@yield('scripts')
</body>
</html>
