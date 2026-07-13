@props(['project', 'loop'])

<article class="proj-card r d{{ $loop->index % 4 }}">
  <a href="{{ $project->slug ? route('projects.show', $project->slug) : '#' }}" class="proj-card-thumb-link" aria-label="View case study for {{ $project->title }}">
    <div class="proj-thumb skeleton">
      @if ($project->image_path)
        @php
          $rawPath = ltrim($project->image_path, '/');
          $webpPath = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $rawPath);
        @endphp
        <picture>
          @if ($rawPath !== $webpPath && file_exists(public_path($webpPath)))
            <source srcset="{{ asset($webpPath) }}" type="image/webp">
          @endif
          <img class="proj-image" src="{{ asset($rawPath) }}" alt="{{ $project->title }}" width="600" height="380" loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async" fetchpriority="{{ $loop->first ? 'high' : 'auto' }}" onload="this.closest('.proj-thumb').classList.remove('skeleton')">
        </picture>
      @else
        <div class="proj-thumb-text">{{ $project->initials ?: 'KH' }}</div>
      @endif
      <div class="proj-arrow">
        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
          <line x1="7" y1="17" x2="17" y2="7"/>
          <polyline points="7 7 17 7 17 17"/>
        </svg>
      </div>
    </div>
  </a>
  <div class="proj-body">
    <div class="proj-idx">/ {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
    <h3 class="proj-title">
      <a href="{{ $project->slug ? route('projects.show', $project->slug) : '#' }}">{{ $project->title }}</a>
    </h3>
    <div class="proj-tags">
      @foreach (($project->tech_stack ?? []) as $tech)
        <span class="p-tag">{{ $tech }}</span>
      @endforeach
    </div>
    <p class="proj-desc">{{ Str::limit(strip_tags($project->description), 260) }}</p>
    <div class="proj-links">
      <a href="{{ $project->slug ? route('projects.show', $project->slug) : '#' }}" class="pl-details">View Details</a>
      @if ($project->url && $project->url !== '#')
        <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="pl-live">Live Demo</a>
      @endif
    </div>
  </div>
</article>
