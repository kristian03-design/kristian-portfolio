<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>All Projects &mdash; Kristian Hernandez</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/chibi-logo.png') }}">
  <meta name="description" content="A list of full-stack web, mobile, and software engineering projects by Kristian Lloyd D.C. Hernandez.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  @vite(['resources/css/portfolio.css', 'resources/js/portfolio.js'])
</head>
<body class="dark-page">
@php
    use Illuminate\Support\Str;

    $projectInitials = function ($title) {
        $words = preg_split('/\s+/', trim($title));
        return collect($words)->filter()->take(2)->map(fn($word) => strtoupper(substr($word, 0, 1)))->implode('');
    };
@endphp

<div id="progress"></div>

<nav id="nav">
  <div class="nav-inner">
    <a href="{{ route('home') }}" class="nav-logo">Kristian<span class="nav-logo-dot">.</span></a>
    <div class="nav-right">
      <a href="{{ route('home') }}" class="nav-resume">&larr; Back to Home</a>
    </div>
  </div>
</nav>

<section id="projects" style="padding-top: clamp(7rem, 10vw, 9rem); min-height: 90vh;">
  <div class="s-in" style="padding-top: 0; padding-bottom: 4.5rem;">
    <div class="proj-header" style="margin-bottom: 3rem;">
      <div class="r in">
        <div class="s-eyebrow">02 &mdash; Portfolio</div>
        <h1 class="s-head">ALL<br>PROJECTS.</h1>
      </div>
    </div>

    <div class="proj-grid">
      @forelse ($projects as $project)
        <article class="proj-card r in d{{ $loop->index % 4 }}">
          <div class="proj-thumb">
            @if ($project->image_path)
              <img class="proj-image" src="{{ asset(ltrim($project->image_path, '/')) }}" alt="{{ $project->title }}">
            @else
              <div class="proj-thumb-text">{{ $projectInitials($project->title) ?: 'KH' }}</div>
            @endif
            <div class="proj-arrow">
              <svg viewBox="0 0 24 24"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </div>
          </div>
          <div class="proj-body">
            <div class="proj-idx">/ {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
            <h3 class="proj-title">{{ $project->title }}</h3>
            <div class="proj-tags">
              @foreach (($project->tech_stack ?? []) as $tech)
                <span class="p-tag">{{ $tech }}</span>
              @endforeach
            </div>
            <p class="proj-desc">{{ Str::limit(strip_tags($project->description), 260) }}</p>
            <div class="proj-links">
              @if ($project->url)
                <a href="{{ $project->url }}" target="_blank" rel="noopener" class="pl-live">Live Demo</a>
              @endif
              @if ($project->github_url)
                <a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="pl-code">Source</a>
              @endif
            </div>
          </div>
        </article>
      @empty
        <div class="empty-state">Projects will appear here once they are published from the CMS.</div>
      @endforelse
    </div>
  </div>
</section>

<footer>
  <span class="footer-logo">Kristian<span>.</span></span>
  <span>&copy; {{ date('Y') }} Kristian Hernandez &mdash; All rights reserved</span>
  <span>Bulacan, Philippines</span>
</footer>
</body>
</html>
