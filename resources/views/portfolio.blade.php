<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kristian Hernandez &mdash; Web &amp; Mobile Developer</title>
<meta name="description" content="Full-stack web and mobile developer based in Bulacan, Philippines. Laravel, Flutter, clean architecture.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/chibi-logo.png') }}">

<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
@vite(['resources/css/portfolio.css', 'resources/js/portfolio.js'])
</head>
<body>
@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Str;

    $projectCount = $projects->count();
    $firstExperience = $experiences->sortBy('start_date')->first();
    $yearsBuilding = $firstExperience
        ? max(1, Carbon::parse($firstExperience->start_date)->diffInYears(now()) + 1)
        : 3;
    $skillsByCategory = $skills->groupBy('category');

    $projectInitials = function ($title) {
        $words = preg_split('/\s+/', trim($title));
        return collect($words)->filter()->take(2)->map(fn($word) => strtoupper(substr($word, 0, 1)))->implode('');
    };

    $skillIconMap = [
        'html' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg',
        'html5' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg',
        'css' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg',
        'css3' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg',
        'javascript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg',
        'js' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg',
        'typescript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg',
        'ts' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg',
        'tailwind' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/tailwindcss/tailwindcss-original.svg',
        'tailwindcss' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/tailwindcss/tailwindcss-original.svg',
        'tailwind css' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/tailwindcss/tailwindcss-original.svg',
        'bootstrap' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg',
        'react' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg',
        'reactjs' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg',
        'react.js' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg',
        'laravel' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/laravel/laravel-original.svg',
        'php' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg',
        'mysql' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg',
        'postgres' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/postgresql/postgresql-original.svg',
        'postgresql' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/postgresql/postgresql-original.svg',
        'node' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg',
        'nodejs' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg',
        'node.js' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg',
        'express' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/express/express-original.svg',
        'expressjs' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/express/express-original.svg',
        'express.js' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/express/express-original.svg',
        'rest api' => 'https://cdn.simpleicons.org/fastapi/009688',
        'restapi' => 'https://cdn.simpleicons.org/fastapi/009688',
        'java' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg',
        'flutter' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/flutter/flutter-original.svg',
        'dart' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/dart/dart-original.svg',
        'android studio' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/androidstudio/androidstudio-original.svg',
        'androidstudio' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/androidstudio/androidstudio-original.svg',
        'git' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg',
        'github' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/github/github-original.svg',
        'git hub' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/github/github-original.svg',
        'docker' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg',
        'figma' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/figma/figma-original.svg',
        'postman' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/postman/postman-original.svg',
        'vercel' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vercel/vercel-original.svg',
        'visual studio code' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vscode/vscode-original.svg',
        'vscode' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vscode/vscode-original.svg',
        'vs code' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vscode/vscode-original.svg',
        'visualstudiocode' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vscode/vscode-original.svg',
        'vite' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vitejs/vitejs-original.svg',
        'vitejs' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vitejs/vitejs-original.svg',
        'vite.js' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vitejs/vitejs-original.svg',
        'xampp' => 'https://cdn.simpleicons.org/xampp/FB7A24',
        'vue' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg',
        'vuejs' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg',
        'vue.js' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg',
        'firebase' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/firebase/firebase-original.svg',
        'supabase' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/supabase/supabase-original.svg',
    ];

    $skillIcon = function ($skill) use ($skillIconMap) {
        if ($skill->icon_svg) {
            return $skill->icon_svg;
        }

        $key = strtolower(trim(preg_replace('/\s+/', ' ', $skill->name)));
        $compactKey = str_replace([' ', '-', '_'], '', $key);

        return $skillIconMap[$key] ?? $skillIconMap[$compactKey] ?? null;
    };

    $dateRange = function ($experience) {
        $start = Carbon::parse($experience->start_date)->format('M Y');
        $end = $experience->is_current || ! $experience->end_date
            ? 'Now'
            : Carbon::parse($experience->end_date)->format('M Y');

        return $start . '<br>&mdash; ' . $end;
    };
@endphp

<div id="progress"></div>

<div id="mobile-menu">
  <a href="#about" class="mm-link">About</a>
  <a href="#projects" class="mm-link">Projects</a>
  <a href="#skills" class="mm-link">Skills</a>
  <a href="#certifications" class="mm-link">Certifications</a>
  <a href="#experience" class="mm-link">Experience</a>
  <a href="#contact" class="mm-link">Contact</a>
</div>

<nav id="nav">
  <div class="nav-inner">
    <a href="#hero" class="nav-logo">Kristian<span class="nav-logo-dot">.</span></a>
    <ul class="nav-links">
      <li><a href="#about" data-section="about">About</a></li>
      <li><a href="#projects" data-section="projects">Projects</a></li>
      <li><a href="#skills" data-section="skills">Skills</a></li>
      <li><a href="#certifications" data-section="certifications">Certifications</a></li>
      <li><a href="#experience" data-section="experience">Experience</a></li>
      <li><a href="#contact" data-section="contact">Contact</a></li>
    </ul>
    <div class="nav-right">
      <a href="{{ asset('resume/HERNANDEZ_KRISTIAN_RESUME 2025.pdf') }}" class="nav-resume">Resume &darr;</a>
      <button class="nav-burger" id="burger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

<section id="hero">
  <div class="hero-ghost">KH</div>

  <div class="hero-content">
    <div class="hero-left">
      <div class="status-pill">
        <span class="status-dot"></span>
        Available &mdash; Open to entry-level roles
      </div>
      <h1 class="hero-title">KRISTIAN<br>HERNANDEZ</h1>
    </div>

    <div class="hero-right">
      <div class="hero-role-tag">Web Developer</div>
      <p class="hero-intro">
        Hi, I'm <strong>Kristian</strong>. I turn ideas into functional digital products &mdash; from responsive interfaces and robust backend systems to cross-platform mobile applications. My goal is to build technology that creates measurable impact and exceptional user experiences. Based in <strong>Bulacan, Philippines</strong>.
      </p>
      <div class="hero-ctas">
        <a href="#projects" class="cta-fill">
          Explore Work
          <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
        <a href="#contact" class="cta-line">Let&rsquo;s Talk</a>
      </div>
    </div>
  </div>

  <div class="hero-bar">
    <div class="h-stat">
      <div class="h-num">{{ $projectCount }}+</div>
      <div class="h-label">Projects shipped</div>
    </div>
    <div class="h-stat">
      <div class="h-num">6+</div>
      <div class="h-label">Months building</div>
    </div>
    <div class="h-stat">
      <div class="h-num">70%</div>
      <div class="h-label">Full-stack capable</div>
    </div>
  </div>
</section>

<div class="marquee-strip">
  <div class="marquee-track">
    @foreach (['Laravel', 'Flutter', 'Full-Stack Developer', 'MySQL', 'Tailwind CSS', 'REST API', 'Bulacan, PH', 'Open to Work', 'Laravel', 'Flutter', 'Full-Stack Developer', 'MySQL', 'Tailwind CSS', 'REST API', 'Bulacan, PH', 'Open to Work'] as $item)
      <span>{{ $item }}</span><span class="dot">&bull;</span>
    @endforeach
  </div>
</div>

<section id="services">
  <div class="s-in services-in">
    <div class="services-grid">
      @foreach ($services as $service)
        <div class="svc-cell" data-num="{{ $service['num'] }}">
          <div class="svc-icon">
            <svg viewBox="0 0 24 24">{!! $service['icon'] !!}</svg>
          </div>
          <div class="svc-title">{{ $service['title'] }}</div>
          <div class="svc-body">{{ $service['body'] }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<hr class="s-rule">

<section id="about">
  <div class="s-in">
    <div class="r">
      <div class="s-eyebrow">01 &mdash; About</div>
      <h2 class="s-head">WHO<br>I AM.</h2>
    </div>

    <div class="r d1">
      <div class="about-body">
        <p>I’m Kristian Lloyd D.C. Hernandez, a BS Information Technology graduate with a passion for building practical software solutions that solve real-world problems.</p>
        <p>My experience spans full-stack web development, database design, API integration, and responsive user interfaces. During my internship and personal projects, I developed systems that streamlined manual processes, improved data management, and enhanced user experience. </p>
        <p>Currently, I specialize in <strong>Laravel</strong>, <strong>PHP</strong>, <strong>MySQL</strong>, <strong>HTML</strong>, <strong>CSS</strong>, <strong>Javascript</strong>, and building full-stack systems from the ground up. I am committed to writing clean, maintainable code and delivering reliable digital solutions.</p>

        <div class="about-chips">
          @forelse ($skills->take(8) as $skill)
            <span class="chip">{{ $skill->name }}</span>
          @empty
            <span class="chip">Laravel / PHP</span>
            <span class="chip">Flutter / Dart</span>
            <span class="chip">MySQL</span>
            <span class="chip">REST APIs</span>
          @endforelse
        </div>
      </div>

      <div class="about-grid">
        <div class="a-card">
          <div class="a-num">01</div>
          <div class="a-title">Backend</div>
          <div class="a-body">Develop secure APIs, database architectures, authentication systems, and scalable business logic using Laravel, PHP, and MySQL.</div>
        </div>
        <div class="a-card">
          <div class="a-num">02</div>
          <div class="a-title">Mobile</div>
          <div class="a-body">Build cross-platform mobile applications with Flutter, focusing on performance, usability, and seamless user experiences.</div>
        </div>
        <div class="a-card">
          <div class="a-num">03</div>
          <div class="a-title">Frontend</div>
          <div class="a-body">Create responsive and modern interfaces using HTML, CSS, JavaScript, and Tailwind CSS with attention to accessibility and design quality.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<hr class="s-rule">

<section id="projects">
  <div class="s-in">
    <div class="proj-header">
      <div class="r">
        <div class="s-eyebrow">02 &mdash; Work</div>
        <h2 class="s-head">FEATURED<br>PROJECTS.</h2>
      </div>
      <a href="{{ route('projects.index') }}" class="proj-gh-link r d1">
        View All
        <svg viewBox="0 0 24 24"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
      </a>
    </div>

    <div class="proj-grid">
      @forelse ($projects as $project)
        <article class="proj-card r d{{ $loop->index % 4 }}">
          <div class="proj-thumb">
            @if ($project->image_path)
              <img class="proj-image" src="{{ asset(ltrim($project->image_path, '/')) }}" alt="{{ $project->title }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async" fetchpriority="{{ $loop->first ? 'high' : 'auto' }}">
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

<hr class="s-rule">

<section id="skills">
  <div class="s-in">
    <div class="r">
      <div class="s-eyebrow">03 &mdash; Toolkit</div>
      <h2 class="s-head">TECH<br>SKILLS.</h2>
    </div>

    <div class="skills-col r d1">
      @forelse ($skillsByCategory as $category => $group)
        <div class="sk-group">
          <div class="sk-group-label">{{ $category }}</div>
          <div class="sk-icon-grid">
            @foreach ($group as $skill)
              @php
                $icon = $skillIcon($skill);
              @endphp
              <div class="sk-icon-cell">
                @if ($icon)
                  <img class="sk-icon-img" src="{{ $icon }}" alt="{{ $skill->name }}" loading="lazy" decoding="async">
                @else
                  <span class="sk-icon-fallback">{{ strtoupper(substr($skill->name, 0, 2)) }}</span>
                @endif
                <span class="sk-icon-name">{{ $skill->name }}</span>
              </div>
            @endforeach
          </div>
        </div>
      @empty
        <div class="empty-state light">Skills will appear here once they are added from the CMS.</div>
      @endforelse
    </div>
  </div>
</section>

<hr class="s-rule">

<section id="experience">
  <div class="s-in">
    <div class="r">
      <div class="s-eyebrow">04 &mdash; Timeline</div>
      <h2 class="s-head">EXPERIENCE<br>HISTORY.</h2>
      <p class="section-note">Academic, professional, and project milestones that shaped how I build.</p>
    </div>

    <div class="exp-list r d1">
      @forelse ($experiences as $experience)
        <div class="exp-row">
          <div class="exp-yr">
            @if ($experience->is_current)
              <div class="exp-curr"></div>
            @endif
            {!! $dateRange($experience) !!}
          </div>
          <div>
            <div class="exp-role">{{ $experience->role }}</div>
            <div class="exp-company">{{ $experience->company }}</div>
            @if ($experience->description)
              <div class="exp-desc">{{ $experience->description }}</div>
            @endif
          </div>
        </div>
      @empty
        <div class="empty-state light">Experience entries will appear here once they are added from the CMS.</div>
      @endforelse
    </div>
  </div>
</section>

<hr class="s-rule">

<section id="certifications">
  <div class="s-in">
    <div class="proj-header cert-header">
      <div class="r">
        <div class="s-eyebrow">05 &mdash; Credentials</div>
        <h2 class="s-head">CERTIFICATIONS.</h2>
      </div>
      <a href="#certifications" class="proj-gh-link cert-view-link r d1">
        View All
        <svg viewBox="0 0 24 24"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
      </a>
    </div>

    <div class="cert-grid r d1">
      @forelse ($certifications as $cert)
        @php
          $certPath = $cert->image_path ? ltrim($cert->image_path, '/') : null;
          $certUrl = $certPath ? asset($certPath) : null;
          $certExt = $certPath ? strtolower(pathinfo($certPath, PATHINFO_EXTENSION)) : null;
          $isPdfCert = $certExt === 'pdf';
        @endphp
        <div class="cert-card">
          @if($certUrl)
            @if($isPdfCert)
              <a href="{{ $certUrl }}" target="_blank" rel="noopener" class="cert-file-wrap" aria-label="Open certificate PDF: {{ $cert->title }}">
                <div class="cert-file-icon">
                  <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="8" y1="13" x2="16" y2="13"/>
                    <line x1="8" y1="17" x2="13" y2="17"/>
                  </svg>
                </div>
                <div>
                  <span class="cert-file-label">PDF Certificate</span>
                  <span class="cert-file-action">Open document</span>
                </div>
              </a>
            @else
              <div class="cert-img-wrap"
                data-lightbox-url="{{ $certUrl }}"
                data-lightbox-caption="{{ $cert->title }} — {{ $cert->issuer }}"
                onclick="openLightbox(this.dataset.lightboxUrl, this.dataset.lightboxCaption)">
                <img src="{{ $certUrl }}" alt="{{ $cert->title }}" class="cert-img" loading="lazy" decoding="async">
                <div class="cert-img-overlay">
                  <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                  </svg>
                  <span>View Certificate</span>
                </div>
              </div>
            @endif
          @endif
          <div class="cert-date">
            {{ \Carbon\Carbon::parse($cert->issue_date)->format('M Y') }}
            @if($cert->expiration_date)
              &mdash; {{ \Carbon\Carbon::parse($cert->expiration_date)->format('M Y') }}
            @else
              &mdash; PRESENT
            @endif
          </div>
          <h3 class="cert-title">{{ $cert->title }}</h3>
          <div class="cert-issuer">{{ $cert->issuer }}</div>
          @if($cert->description)
            <p class="cert-desc">{{ $cert->description }}</p>
          @endif
          @if($cert->credential_id)
            <div class="cert-id">ID: <span>{{ $cert->credential_id }}</span></div>
          @endif
          @if($cert->credential_url)
            <a href="{{ $cert->credential_url }}" target="_blank" rel="noopener" class="cert-link">
              View Credential
              <svg viewBox="0 0 24 24"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </a>
          @endif
        </div>
      @empty
        <div class="empty-state light">Certifications will appear here once they are added from the CMS.</div>
      @endforelse
    </div>
  </div>
</section>

<hr class="s-rule">

<section id="contact">
  <div class="s-in">
    <div class="r">
      <div class="s-eyebrow">06 &mdash; Contact</div>
      <h2 class="s-head">LET&rsquo;S<br>TALK.</h2>
      <p class="contact-sub">Open to junior dev roles, contract work, and collaborations. Drop a message and I&rsquo;ll get back to you.</p>

      <a href="mailto:hkristianlloyd2@gmail.com" class="contact-link">
        <div class="c-icon">
          <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="2,4 12,13 22,4"/></svg>
        </div>
        hkristianlloyd2@gmail.com
      </a>
      <div class="contact-link contact-static">
        <div class="c-icon">
          <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
        </div>
        Bulacan, Philippines
      </div>

      <div class="socials">
        <a href="https://github.com/kristian03-design" class="social-btn" aria-label="GitHub">
          <svg viewBox="0 0 24 24"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
        </a>
        <a href="https://www.linkedin.com/in/hernandez-kristian/" class="social-btn" aria-label="LinkedIn">
          <svg viewBox="0 0 24 24"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
        </a>
        <a href="https://www.facebook.com/klqtie" class="social-btn" aria-label="Facebook">
          <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        </a>
      </div>
    </div>

    <form class="cform r d2" id="contactForm" method="POST" action="{{ route('contact.store') }}">
      @csrf
      <div id="formMessage" class="form-message hidden"></div>
      <div class="cf-row">
        <div class="cf-grp cf-flat">
          <label class="cf-lbl" for="contactName">Name</label>
          <input id="contactName" name="name" type="text" class="cf-input" placeholder="Your name" required>
        </div>
        <div class="cf-grp cf-flat">
          <label class="cf-lbl" for="contactEmail">Email</label>
          <input id="contactEmail" name="email" type="email" class="cf-input" placeholder="your@email.com" required>
        </div>
      </div>
      <div class="cf-grp">
        <label class="cf-lbl" for="contactSubject">Subject</label>
        <input id="contactSubject" name="subject" type="text" class="cf-input" placeholder="Project inquiry / Job offer / Collab">
      </div>
      <div class="cf-grp">
        <label class="cf-lbl" for="contactMessage">Message</label>
        <textarea id="contactMessage" name="message" class="cf-input" rows="5" placeholder="Tell me what you need..." required></textarea>
      </div>
      <button class="cf-submit" type="submit">
        Send Message
        <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
      </button>
    </form>
  </div>
</section>

<footer>
  <span class="footer-logo">Kristian<span>.</span></span>
  <span>&copy; {{ date('Y') }} Kristian Hernandez &mdash; All rights reserved</span>
  <span>Bulacan, Philippines</span>
</footer>
<!-- Lightbox Modal for Certifications -->
<div id="portfolio-lightbox" class="lightbox-modal" onclick="closeLightbox()">
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

</body>
</html>
