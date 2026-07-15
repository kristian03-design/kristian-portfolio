@php
  $isHome = Route::is('home');
  $link = function ($anchor) use ($isHome) {
      return $isHome ? "#{$anchor}" : route('home') . "#{$anchor}";
  };

  // Resume cache busting
  $resumeVersion = '1.0.0';
  $resumeFilePath = public_path('resume/HERNANDEZ_KRISTIAN_RESUME_2025.pdf');
  if (file_exists($resumeFilePath)) {
      $resumeVersion = substr(md5_file($resumeFilePath) ?: '1.0.0', 0, 8);
  }
@endphp

<nav id="nav">
  <div class="nav-inner">
    <a href="{{ route('home') }}" class="nav-logo">Kristian<span class="nav-logo-dot">.</span></a>
    
    @if ($isHome)
      <ul class="nav-links">
        <li><a href="#about" data-section="about">About</a></li>
        <li><a href="#projects" data-section="projects">Projects</a></li>
        <li><a href="#skills" data-section="skills">Skills</a></li>
        <li><a href="#experience" data-section="experience">Experience</a></li>
        <li><a href="#beyond-code" data-section="beyond-code">Beyond Code</a></li>
        <li><a href="#certifications" data-section="certifications">Certifications</a></li>
        <li><a href="#contact" data-section="contact">Contact</a></li>
      </ul>
    @else
      <ul class="nav-links">
        <li><a href="{{ route('home') }}#about">About</a></li>
        <li><a href="{{ route('home') }}#projects">Projects</a></li>
        <li><a href="{{ route('home') }}#skills">Skills</a></li>
        <li><a href="{{ route('home') }}#experience">Experience</a></li>
        <li><a href="{{ route('home') }}#beyond-code">Beyond Code</a></li>
        <li><a href="{{ route('home') }}#certifications">Certifications</a></li>
        <li><a href="{{ route('home') }}#contact">Contact</a></li>
      </ul>
    @endif

    <div class="nav-right">
      <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle theme">
        <span class="sun-wrapper"><i data-lucide="sun" class="sun-icon" style="width: 16px; height: 16px;"></i></span>
        <span class="moon-wrapper"><i data-lucide="moon" class="moon-icon" style="width: 16px; height: 16px;"></i></span>
      </button>

      <div class="nav-resume-wrapper">
        <a href="{{ route('resume.view') }}" target="_blank" rel="noopener noreferrer" class="nav-resume-premium btn-view-resume" aria-label="View Resume in a new tab">
          <span class="btn-icon"></span> <span class="btn-text">View Resume</span> <i data-lucide="arrow-up-right" style="width: 16px; height: 16px; display: inline-block; vertical-align: -1px; margin-left: 2.5px;"></i>
        </a>
        <a href="{{ route('resume.download') }}" class="nav-resume-premium btn-download-resume" title="Download Resume" aria-label="Download Resume PDF">
          <i data-lucide="download" style="width: 16px; height: 16px;"></i>
        </a>
      </div>
      <button class="nav-burger" id="burger" aria-label="Menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>
</nav>
