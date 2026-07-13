@extends('layouts.app')

@section('title', $project->title . ' — Case Study & Project Details')
@section('meta_description', Str::limit(strip_tags($project->description), 160))

@section('mobile_menu_links')
  <a href="{{ route('home') }}#about" class="mm-link">About</a>
  <a href="{{ route('home') }}#projects" class="mm-link">Projects</a>
  <a href="{{ route('home') }}#skills" class="mm-link">Skills</a>
  <a href="{{ route('home') }}#certifications" class="mm-link">Certifications</a>
  <a href="{{ route('home') }}#experience" class="mm-link">Experience</a>
  <a href="{{ route('home') }}#contact" class="mm-link">Contact</a>
@endsection

@section('navigation')
  <x-nav />
@endsection

@section('content')
<div class="project-details-page" style="padding-top: clamp(6rem, 8vw, 8rem); padding-bottom: 6rem;">
  <div class="s-in" style="padding-top: 0; padding-bottom: 0;">
    
    <!-- Breadcrumbs & Back Button -->
    <nav class="project-breadcrumbs" aria-label="Breadcrumb">
      <ol class="breadcrumbs-list">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li class="separator">/</li>
        <li><a href="{{ route('projects.index') }}">Projects</a></li>
        <li class="separator">/</li>
        <li class="current" aria-current="page">{{ $project->title }}</li>
      </ol>
      <a href="{{ route('projects.index') }}" class="back-link">
        <i data-lucide="arrow-left" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 4.5px;"></i> Back to Projects
      </a>
    </nav>

    <!-- Project Hero Section -->
    <header class="project-hero r in">
      <div class="project-hero-grid">
        <div class="project-hero-left">
          <span class="category-pill">{{ $project->category ?? 'Software Project' }}</span>
          <h1 class="project-title-large">{{ $project->title }}</h1>
          
          <div class="project-meta-strip">
            <div class="meta-item">
              <span class="meta-label">Status</span>
              <span class="meta-val status-badge {{ Str::slug($project->status ?? 'unknown') }}">
                <span class="status-dot"></span> {{ $project->status ?? 'Completed' }}
              </span>
            </div>
            <div class="meta-item">
              <span class="meta-label">Duration</span>
              <span class="meta-val">{{ $project->duration ?? '3 Months' }}</span>
            </div>
            <div class="meta-item">
              <span class="meta-label">Role</span>
              <span class="meta-val">{{ $project->role ?? 'Solo Developer' }}</span>
            </div>
          </div>

          <div class="hero-tech-badges">
            @foreach (($project->tech_stack ?? []) as $tech)
              <span class="p-tag">{{ $tech }}</span>
            @endforeach
          </div>

          <div class="project-ctas">
            @if($project->url && $project->url !== '#')
              <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="pl-live">Live Demo</a>
            @else
              <button class="pl-live" disabled style="opacity: 0.5; cursor: not-allowed;">Demo Offline</button>
            @endif

            @if($project->github_url)
              <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="pl-code">GitHub Repo</a>
            @else
              <button class="pl-code" disabled style="opacity: 0.5; cursor: not-allowed;">Private Source</button>
            @endif

            @if($project->documentation_url)
              <a href="{{ $project->documentation_url }}" target="_blank" rel="noopener noreferrer" class="pl-code">Documentation</a>
            @else
              <button class="pl-code" disabled style="opacity: 0.5; cursor: not-allowed;">No Docs</button>
            @endif
          </div>
        </div>

        <div class="project-hero-right">
          <div class="featured-image-wrapper skeleton">
            @if($project->image_path)
              @php
                $rawPath = ltrim($project->image_path, '/');
                $webpPath = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $rawPath);
              @endphp
              <picture>
                @if ($rawPath !== $webpPath && file_exists(public_path($webpPath)))
                  <source srcset="{{ asset($webpPath) }}" type="image/webp">
                @endif
                <img src="{{ asset($rawPath) }}" alt="{{ $project->title }} Main Screenshot" class="project-hero-img" width="800" height="500" loading="eager" decoding="async" fetchpriority="high" onload="this.parentElement.classList.remove('skeleton')" onclick="openLightbox(this.src, '{{ $project->title }} — Main View')">
              </picture>
            @else
              <div class="project-hero-fallback">{{ $project->initials }}</div>
            @endif
          </div>
        </div>
      </div>
    </header>

    <hr class="s-rule" style="margin: 4rem 0;">

    <!-- Metrics Cards Grid -->
    <section class="details-section r d1">
      <h2 class="section-title-small">Project Metrics</h2>
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-num">{{ $project->metrics['loc'] ?? 'N/A' }}</div>
          <div class="metric-lbl">Lines of Code</div>
        </div>
        <div class="metric-card">
          <div class="metric-num">{{ $project->metrics['db_tables'] ?? 'N/A' }}</div>
          <div class="metric-lbl">Database Tables</div>
        </div>
        <div class="metric-card">
          <div class="metric-num">{{ $project->metrics['api_endpoints'] ?? 'N/A' }}</div>
          <div class="metric-lbl">API Endpoints</div>
        </div>
        <div class="metric-card">
          <div class="metric-num">{{ $project->metrics['modules'] ?? 'N/A' }}</div>
          <div class="metric-lbl">Core Modules</div>
        </div>
        <div class="metric-card">
          <div class="metric-num">{{ $project->metrics['completion_date'] ?? 'N/A' }}</div>
          <div class="metric-lbl">Completion Date</div>
        </div>
        <div class="metric-card">
          <div class="metric-num">{{ $project->metrics['development_time'] ?? 'N/A' }}</div>
          <div class="metric-lbl">Development Time</div>
        </div>
      </div>
    </section>

    <hr class="s-rule" style="margin: 4rem 0;">

    <!-- Overview Section -->
    <section class="details-section r d1">
      <h2 class="section-title-small">Project Overview</h2>
      <div class="overview-grid">
        <div class="overview-main">
          <h3 class="overview-sub-title">What is it?</h3>
          <p>{{ $project->overview['what'] ?? $project->description }}</p>
          
          <h3 class="overview-sub-title" style="margin-top: 2rem;">Why was it built?</h3>
          <p>{{ $project->overview['why'] ?? 'To solve a workflow limitation and increase digital capabilities.' }}</p>
        </div>
        <div class="overview-sidebar">
          <div class="sidebar-block">
            <h4>Target Users</h4>
            <p>{{ $project->overview['target_users'] ?? 'General Public & Administrators' }}</p>
          </div>
          <div class="sidebar-block">
            <h4>Business Purpose</h4>
            <p>{{ $project->overview['business_purpose'] ?? 'Streamlining processing and records management.' }}</p>
          </div>
          <div class="sidebar-block">
            <h4>Expected Outcome</h4>
            <p>{{ $project->overview['expected_outcome'] ?? 'Highly efficient and secure operations.' }}</p>
          </div>
        </div>
      </div>
    </section>

    <hr class="s-rule" style="margin: 4rem 0;">

    <!-- Gallery Carousel Section -->
    @if(!empty($project->gallery))
      <section class="details-section r d1">
        <h2 class="section-title-small">Interface Gallery</h2>
        <div class="gallery-carousel-wrapper">
          <div class="carousel-viewport">
            <div class="carousel-slides" id="carousel-slides">
              @foreach($project->gallery as $device => $path)
                <div class="carousel-slide {{ $loop->first ? 'active' : '' }}" data-device="{{ $device }}">
                  @php
                    $rawPath = ltrim($path, '/');
                    $webpPath = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $rawPath);
                  @endphp
                  <picture>
                    @if ($rawPath !== $webpPath && file_exists(public_path($webpPath)))
                      <source srcset="{{ asset($webpPath) }}" type="image/webp">
                    @endif
                    <img src="{{ asset($rawPath) }}" alt="{{ $project->title }} {{ ucfirst($device) }} View" class="carousel-img" width="800" height="500" loading="lazy" decoding="async" onclick="openLightbox(this.src, '{{ $project->title }} — {{ ucfirst($device) }} View')">
                  </picture>
                  <div class="slide-badge">{{ ucfirst($device) }} View</div>
                </div>
              @endforeach
            </div>
          </div>
          <button class="carousel-btn prev" id="carousel-prev" aria-label="Previous image">
            <i data-lucide="chevron-left"></i>
          </button>
          <button class="carousel-btn next" id="carousel-next" aria-label="Next image">
            <i data-lucide="chevron-right"></i>
          </button>
          
          <!-- Carousel Thumbnails -->
          <div class="carousel-thumbnails">
            @foreach($project->gallery as $device => $path)
              <button class="carousel-thumb-btn {{ $loop->first ? 'active' : '' }}" onclick="goToSlide({{ $loop->index }})" aria-label="View {{ $device }} screenshot">
                {{ ucfirst($device) }}
              </button>
            @endforeach
          </div>
        </div>
      </section>

      <hr class="s-rule" style="margin: 4rem 0;">
    @endif

    <!-- Features Section -->
    @if(!empty($project->features))
      <section class="details-section r d1">
        <h2 class="section-title-small">Key Features</h2>
        <div class="features-grid">
          @foreach($project->features as $feature)
            <div class="feature-card">
              <div class="feature-icon">
                <i data-lucide="{{ $feature['icon'] ?? 'check-circle' }}"></i>
              </div>
              <h3 class="feature-title">{{ $feature['title'] }}</h3>
              <p class="feature-desc">{{ $feature['description'] }}</p>
            </div>
          @endforeach
        </div>
      </section>

      <hr class="s-rule" style="margin: 4rem 0;">
    @endif

    <!-- Challenges Section -->
    @if(!empty($project->challenges))
      <section class="details-section r d1">
        <h2 class="section-title-small">Challenges & Resolutions</h2>
        <div class="challenges-track">
          <div class="challenge-node problem">
            <div class="node-icon"><i data-lucide="alert-triangle"></i></div>
            <div class="node-content">
              <h3>The Problem</h3>
              <p>{{ $project->challenges['problem'] }}</p>
            </div>
          </div>
          <div class="challenge-node solution">
            <div class="node-icon"><i data-lucide="lightbulb"></i></div>
            <div class="node-content">
              <h3>The Solution</h3>
              <p>{{ $project->challenges['solution'] }}</p>
            </div>
          </div>
          <div class="challenge-node result">
            <div class="node-icon"><i data-lucide="check-circle-2"></i></div>
            <div class="node-content">
              <h3>The Result</h3>
              <p>{{ $project->challenges['result'] }}</p>
            </div>
          </div>
        </div>
      </section>

      <hr class="s-rule" style="margin: 4rem 0;">
    @endif

    <!-- Development Timeline -->
    @if(!empty($project->timeline))
      <section class="details-section r d1">
        <h2 class="section-title-small">Development Timeline</h2>
        <div class="timeline-container">
          @foreach($project->timeline as $phase => $desc)
            <div class="timeline-step">
              <div class="step-num">{{ $loop->iteration }}</div>
              <div class="step-content">
                <h3>{{ ucfirst($phase) }}</h3>
                <p>{{ $desc }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </section>

      <hr class="s-rule" style="margin: 4rem 0;">
    @endif

    <!-- Performance & Optimizations -->
    @if(!empty($project->performance))
      <section class="details-section r d1">
        <h2 class="section-title-small">Performance & Optimization</h2>
        <div class="performance-grid">
          <div class="performance-card-scores">
            <div class="score-radial">
              <div class="score-num">{{ $project->performance['performance_score'] ?? 90 }}</div>
              <div class="score-lbl">Performance</div>
            </div>
            <div class="score-radial">
              <div class="score-num">{{ $project->performance['accessibility'] ?? 90 }}</div>
              <div class="score-lbl">Accessibility</div>
            </div>
          </div>
          <div class="performance-details-list">
            <div class="perf-item">
              <strong>Lazy Loading:</strong>
              <span>{{ $project->performance['lazy_loading'] }}</span>
            </div>
            <div class="perf-item">
              <strong>Caching Strategy:</strong>
              <span>{{ $project->performance['caching'] }}</span>
            </div>
            <div class="perf-item">
              <strong>Image Optimization:</strong>
              <span>{{ $project->performance['image_optimization'] }}</span>
            </div>
            <div class="perf-item">
              <strong>SEO Optimization:</strong>
              <span>{{ $project->performance['seo'] }}</span>
            </div>
          </div>
        </div>
      </section>

      <hr class="s-rule" style="margin: 4rem 0;">
    @endif

    <!-- Security Configurations -->
    @if(!empty($project->security_details))
      <section class="details-section r d1">
        <h2 class="section-title-small">Security Implementations</h2>
        <div class="security-flex-grid">
          @foreach($project->security_details as $guard => $detail)
            <div class="security-chip-card">
              <div class="sec-label">{{ strtoupper(str_replace('_', ' ', $guard)) }}</div>
              <p class="sec-detail">{{ $detail }}</p>
            </div>
          @endforeach
        </div>
      </section>

      <hr class="s-rule" style="margin: 4rem 0;">
    @endif

    <!-- Full Video Demo Section -->
    <section class="details-section r d1">
      <h2 class="section-title-small">Video Demonstration</h2>
      <div class="video-container skeleton">
        @if($project->video_demo_url)
          <iframe class="video-iframe" src="{{ $project->video_demo_url }}" title="{{ $project->title }} Video Demo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen onload="this.parentElement.classList.remove('skeleton')"></iframe>
        @else
          <div class="video-fallback">
            <div class="fallback-icon">🎥</div>
            <h3>Video Demo Unavailable</h3>
            <p>A video recording of this application is not currently available.</p>
          </div>
        @endif
      </div>
    </section>

    <hr class="s-rule" style="margin: 5rem 0 4rem 0;">

    <!-- Related Projects -->
    <section class="related-projects-section r d1">
      <h2 class="section-title-small" style="margin-bottom: 2.5rem;">You may also like</h2>
      <div class="proj-grid">
        @foreach($relatedProjects as $relProject)
          <x-project-card :project="$relProject" :loop="$loop" />
        @endforeach
      </div>
    </section>

  </div>
</div>
@endsection

@section('scripts')
<script>
  let currentSlideIndex = 0;
  const slides = document.querySelectorAll('.carousel-slide');
  const thumbs = document.querySelectorAll('.carousel-thumb-btn');

  function showSlide(index) {
    if (slides.length === 0) return;
    
    if (index >= slides.length) currentSlideIndex = 0;
    else if (index < 0) currentSlideIndex = slides.length - 1;
    else currentSlideIndex = index;

    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === currentSlideIndex);
    });

    thumbs.forEach((thumb, i) => {
      thumb.classList.toggle('active', i === currentSlideIndex);
    });
  }

  function nextSlide() {
    showSlide(currentSlideIndex + 1);
  }

  function prevSlide() {
    showSlide(currentSlideIndex - 1);
  }

  document.getElementById('carousel-next')?.addEventListener('click', nextSlide);
  document.getElementById('carousel-prev')?.addEventListener('click', prevSlide);

  function goToSlide(index) {
    showSlide(index);
  }

  // Keyboard navigation for carousel
  document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') {
      const activeEl = document.activeElement;
      // only trigger if not focusing form fields
      if (activeEl.tagName !== 'INPUT' && activeEl.tagName !== 'TEXTAREA') {
        nextSlide();
      }
    } else if (e.key === 'ArrowLeft') {
      const activeEl = document.activeElement;
      if (activeEl.tagName !== 'INPUT' && activeEl.tagName !== 'TEXTAREA') {
        prevSlide();
      }
    }
  });
</script>
@endsection
