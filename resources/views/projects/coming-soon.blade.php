@extends('layouts.app')

@section('title', $project->title . ' — Case Study Coming Soon')
@section('meta_description', 'Documentation and case study details for ' . $project->title . ' are coming soon.')

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

    <!-- Coming Soon Premium Block -->
    <section class="details-section r d1" style="max-width: 800px; margin: 0 auto; text-align: center; padding: 4rem 2rem; background: var(--surface); border: 1px solid var(--border); border-radius: 4px; box-shadow: var(--shadow);">
      <div style="font-size: 3rem; margin-bottom: 1.5rem; filter: grayscale(0.2);">📝</div>
      <h2 style="font-family: var(--f-head); font-size: 2.5rem; letter-spacing: 0.02em; margin-bottom: 1rem; color: var(--text-primary);">
        Project Documentation Coming Soon
      </h2>
      
      <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 100px; color: #d97706; font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2rem;">
        <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span> 
        Under Development
      </div>

      <p style="font-size: 0.9rem; line-height: 1.8; color: var(--text-secondary); max-width: 600px; margin: 0 auto 2rem auto; text-align: justify; text-justify: inter-word;">
        This project has already been completed and is available in my portfolio. I'm currently preparing a comprehensive case study that will showcase the project's architecture, design decisions, development process, features, screenshots, technical implementation, and lessons learned. Please check back soon for the full documentation.
      </p>

      <div style="font-size: 0.72rem; color: var(--text-muted); border-top: 1px solid var(--border); padding-top: 1.5rem; display: flex; justify-content: center; gap: 2rem;">
        <span><strong>Last Updated:</strong> {{ $project->updated_at ? $project->updated_at->format('M d, Y') : 'Recently' }}</span>
        <span><strong>Est. Completion:</strong> Q3 2026</span>
      </div>
    </section>

    <!-- Page Navigation Buttons -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 5rem; flex-wrap: wrap; gap: 1.5rem;">
      @if($prevProject)
        <a href="{{ route('projects.show', $prevProject->slug) }}" class="back-link" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; transition: all 0.2s;">
          <i data-lucide="chevron-left" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 4px;"></i> Previous Project
        </a>
      @else
        <span style="opacity: 0.3; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;">
          <i data-lucide="chevron-left" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 4px;"></i> First Project
        </span>
      @endif

      <a href="{{ route('projects.index') }}" class="pl-code" style="padding: 0.6rem 1.4rem; font-size: 0.68rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase;">
        Back to Projects
      </a>

      @if($nextProject)
        <a href="{{ route('projects.show', $nextProject->slug) }}" class="back-link" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; transition: all 0.2s;">
          Next Project <i data-lucide="chevron-right" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-left: 4px;"></i>
        </a>
      @else
        <span style="opacity: 0.3; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;">
          Last Project <i data-lucide="chevron-right" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-left: 4px;"></i>
        </span>
      @endif
    </div>

  </div>
</div>
@endsection
