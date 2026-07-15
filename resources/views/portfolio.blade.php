@extends('layouts.app')

@section('title', 'Kristian Hernandez — Web & Mobile Developer')

@section('mobile_menu_links')
  <a href="#about" class="mm-link">About</a>
  <a href="#projects" class="mm-link">Projects</a>
  <a href="#skills" class="mm-link">Skills</a>
  <a href="#experience" class="mm-link">Experience</a>
  <a href="#beyond-code" class="mm-link">Beyond Code</a>
  <a href="#certifications" class="mm-link">Certifications</a>
  <a href="#contact" class="mm-link">Contact</a>
@endsection

@section('navigation')
  <x-nav />
@endsection

@section('content')
@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Str;

    $projectCount = $projects->count();
    $firstExperience = $experiences->sortBy('start_date')->first();
    $yearsBuilding = $firstExperience
        ? max(1, Carbon::parse($firstExperience->start_date)->diffInYears(now()) + 1)
        : 3;
    $skillsByCategory = $skills->groupBy('category');

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

<section id="hero">
  <div class="hero-ghost">KH</div>

  <div class="hero-content">
    <div class="hero-left">
      <div class="status-pill">
        <span class="status-dot"></span>
        <span class="typewriter-target" data-roles='["Available — Open to entry-level roles", "Available — Open to remote work", "Available — Open to freelance"]'>Available &mdash; Open to entry-level roles</span><span class="typewriter-cursor">|</span>
      </div>
      <h1 class="hero-title">KRISTIAN<br>HERNANDEZ</h1>
    </div>

    <div class="hero-right">
      <div class="hero-role-tag">
        <span class="typewriter-target" data-roles='["Web Developer", "Full-Stack Developer"]'>Web Developer</span><span class="typewriter-cursor">|</span>
      </div>
      <p class="hero-intro">
        Hi, I'm <strong>Kristian</strong>. I turn ideas into functional digital products &mdash; from responsive interfaces and robust backend systems to cross-platform mobile applications. My goal is to build technology that creates measurable impact and exceptional user experiences. Based in <strong>Bulacan, Philippines</strong>.
      </p>
      <div class="hero-ctas">
        <a href="#projects" class="cta-fill">
          Explore Work
          <i data-lucide="arrow-right"></i>
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

<div class="marquee-strip" aria-hidden="true">
  <div class="marquee-track">
    @foreach (['Laravel', 'Flutter', 'Full-Stack Developer', 'MySQL', 'Tailwind CSS', 'REST API', 'Bulacan, PH', 'Open to Work', 'Laravel', 'Flutter', 'Full-Stack Developer', 'MySQL', 'Tailwind CSS', 'REST API', 'Bulacan, PH', 'Open to Work'] as $item)
      <span class="marquee-item">{{ $item }}</span>
      <span class="marquee-dot"></span>
    @endforeach
  </div>
</div>

<section id="services">
  <div class="s-in services-in">
    <div class="services-grid">
      @foreach ($services as $service)
        <div class="svc-cell" data-num="{{ $service['num'] }}">
          <div class="svc-icon">
            <i data-lucide="{{ $service['icon'] }}"></i>
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
        <p>I’m <strong>Kristian Lloyd DC. Hernandez</strong>, a <strong>Bachelor of Science in Information Technology</strong> graduate with a passion for building practical software solutions that solve real-world problems.</p>
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
        <i data-lucide="arrow-up-right"></i>
      </a>
    </div>

    <div class="proj-grid">
      @forelse ($projects as $project)
        <x-project-card :project="$project" :loop="$loop" />
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
      <p class="section-note" style="margin-bottom: 2rem;">A curated collection of languages, frameworks, and tools I use to build robust applications.</p>
      <p style="font-size: 0.9rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 2rem; text-align: justify; text-justify: inter-word;">
        I believe in using the right tool for the job. Over the years, I've developed core proficiencies in full-stack web architectures, cross-platform mobile environments, database design, and cloud workflows, prioritizing system efficiency, security, and clean code maintainability.
      </p>
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
                  <img class="sk-icon-img" src="{{ $icon }}" alt="{{ $skill->name }}" width="32" height="32" loading="lazy" decoding="async">
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

<section id="beyond-code">
  <div class="s-in">
    <!-- Left Column: Eyebrow, Title & Intro -->
    <div class="r">
      <div class="s-eyebrow">05 &mdash; Beyond Code</div>
      <h2 class="s-head">BEYOND<br>THE CODE.</h2>
      <p class="section-note" style="margin-bottom: 2rem;">Curiosity, continuous improvement, and the mindset behind the screens.</p>
      <p style="font-size: 0.9rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 2rem; text-align: justify; text-justify: inter-word;">
        While I spend most of my days building systems and writing code, I believe that being a great developer is about more than just programming. It's about curiosity, consistency, physical discipline, and a mindset of continuous improvement. Here is a glimpse of who I am when the keyboard is idle.
      </p>
    </div>

    <!-- Right Column: Main Cards, Focus & Traits -->
    <div class="beyond-content r d1">
      <h3 class="sk-group-label" style="margin-bottom: 1.5rem;">
        <span>Core Interests &amp; Mindset</span>
      </h3>
      <div class="beyond-grid" style="margin-bottom: 4rem;">
        
        <div class="beyond-card">
          <div class="beyond-card-icon"><i data-lucide="folder-git-2"></i></div>
          <h4 class="beyond-card-title">Personal Projects</h4>
          <p class="beyond-card-text">Building tools that solve real problems. I love turning ideas into fully functional digital products from scratch.</p>
        </div>

        <div class="beyond-card">
          <div class="beyond-card-icon"><i data-lucide="graduation-cap"></i></div>
          <h4 class="beyond-card-title">Continuous Learning</h4>
          <p class="beyond-card-text">Exploring new methodologies, software design patterns, and system architectures to write cleaner, more maintainable code.</p>
        </div>

        <div class="beyond-card">
          <div class="beyond-card-icon"><i data-lucide="music-4"></i></div>
          <h4 class="beyond-card-title">Music &amp; Focus</h4>
          <p class="beyond-card-text">Finding flow state through sound. Lo-fi, classical, and synthwave play in the background of my coding sessions.</p>
        </div>

        <div class="beyond-card">
          <div class="beyond-card-icon"><i data-lucide="gamepad-2"></i></div>
          <h4 class="beyond-card-title">Sports &amp; Online Games</h4>
          <p class="beyond-card-text">Balancing active physical sports with competitive online gaming. Both build strategic thinking, teamwork, and quick reflexes.</p>
        </div>

        <div class="beyond-card">
          <div class="beyond-card-icon"><i data-lucide="brain-circuit"></i></div>
          <h4 class="beyond-card-title">Problem Solving</h4>
          <p class="beyond-card-text">Tackling logic puzzles and algorithms. The thrill of breaking down complex challenges into simple, elegant steps.</p>
        </div>

        <div class="beyond-card">
          <div class="beyond-card-icon"><i data-lucide="rocket"></i></div>
          <h4 class="beyond-card-title">Tech Exploration</h4>
          <p class="beyond-card-text">Keeping tabs on emerging tech. Exploring Docker, CI/CD pipelines, and cloud computing for devops maturity.</p>
        </div>

      </div>

      <h3 class="sk-group-label" style="margin-bottom: 1.5rem;">
        <span>Current Focus &amp; Habits</span>
      </h3>
      <div class="currently-panel" style="margin-bottom: 4rem;">
        <div class="currently-grid">
          <div class="currently-item">
            <span class="currently-label">Learning</span>
            <span class="currently-val">Advanced System Architectures &amp; DevOps Pipelines</span>
          </div>
          <div class="currently-item">
            <span class="currently-label">Building</span>
            <span class="currently-val">High-Performance CMS Utilities &amp; Portfolios</span>
          </div>
          <div class="currently-item">
            <span class="currently-label">Career Goal</span>
            <span class="currently-val">Full-Stack Software Engineer (Open to Entry-Level Roles)</span>
          </div>
          <div class="currently-item">
            <span class="currently-label">Habit</span>
            <span class="currently-val">1 Hour of Reading/Tech Skill practice every single day</span>
          </div>
        </div>
      </div>

      <h3 class="sk-group-label" style="margin-bottom: 1.5rem;">
        <span>Professional Traits</span>
      </h3>
      <div class="badges-row" style="margin-bottom: 4.5rem;">
        <span class="badge-item"><i data-lucide="zap"></i> Fast Learner</span>
        <span class="badge-item"><i data-lucide="puzzle"></i> Problem Solver</span>
        <span class="badge-item"><i data-lucide="users"></i> Team Player</span>
        <span class="badge-item"><i data-lucide="target"></i> Detail-Oriented</span>
        <span class="badge-item"><i data-lucide="palette"></i> UI/UX Enthusiast</span>
        <span class="badge-item"><i data-lucide="code-2"></i> Laravel Developer</span>
        <span class="badge-item"><i data-lucide="trending-up"></i> Growth Mindset</span>
        <span class="badge-item"><i data-lucide="briefcase"></i> Open to Entry-Level Roles</span>
      </div>

      <blockquote class="beyond-quote">
        <i data-lucide="quote" class="quote-icon" style="width: 20px; height: 20px;"></i>
        <p>"Writing code is a craft. Building software that genuinely helps someone is an art. I am committed to continuous learning, refining my skills, and building clean, impactful technology."</p>
      </blockquote>

    </div>
  </div>
</section>

<hr class="s-rule">

<section id="certifications">
  <div class="s-in">
    <div class="proj-header cert-header">
      <div class="r">
        <div class="s-eyebrow">06 &mdash; Credentials</div>
        <h2 class="s-head">CERTIFICATIONS.</h2>
      </div>
      <a href="#certifications" class="proj-gh-link cert-view-link r d1">
        View All
        <i data-lucide="arrow-up-right"></i>
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
                  <i data-lucide="file-text"></i>
                </div>
                <div>
                  <span class="cert-file-label">PDF Certificate</span>
                  <span class="cert-file-action">Open document</span>
                </div>
              </a>
            @else
              @php
                $rawPath = ltrim($cert->image_path, '/');
                $webpPath = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $rawPath);
                $lightboxUrl = ($rawPath !== $webpPath && file_exists(public_path($webpPath))) ? asset($webpPath) : $certUrl;
              @endphp
              <div class="cert-img-wrap skeleton"
                data-lightbox-url="{{ $lightboxUrl }}"
                data-lightbox-caption="{{ $cert->title }} — {{ $cert->issuer }}"
                onclick="openLightbox(this.dataset.lightboxUrl, this.dataset.lightboxCaption)">
                <picture>
                  @if ($rawPath !== $webpPath && file_exists(public_path($webpPath)))
                    <source srcset="{{ asset($webpPath) }}" type="image/webp">
                  @endif
                  <img src="{{ $certUrl }}" alt="{{ $cert->title }}" class="cert-img" width="400" height="280" loading="lazy" decoding="async" onload="this.closest('.cert-img-wrap').classList.remove('skeleton')">
                </picture>
                <div class="cert-img-overlay">
                  <i data-lucide="search"></i>
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
              <i data-lucide="arrow-up-right"></i>
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
      <div class="s-eyebrow">07 &mdash; Contact</div>
      <h2 class="s-head">LET&rsquo;S<br>TALK.</h2>
      <p class="contact-sub">Open to junior dev roles, contract work, and collaborations. Drop a message and I&rsquo;ll get back to you.</p>

      <a href="mailto:hkristianlloyd2@gmail.com" class="contact-link">
        <div class="c-icon">
          <i data-lucide="mail"></i>
        </div>
        hkristianlloyd2@gmail.com
      </a>
      <div class="contact-link contact-static">
        <div class="c-icon">
          <i data-lucide="map-pin"></i>
        </div>
        Bulacan, Philippines
      </div>

      <div class="socials">
        <a href="https://github.com/kristian03-design" class="social-btn" aria-label="GitHub">
          <i data-lucide="github"></i>
        </a>
        <a href="https://www.linkedin.com/in/hernandez-kristian/" class="social-btn" aria-label="LinkedIn">
          <i data-lucide="linkedin"></i>
        </a>
        <a href="https://www.facebook.com/klqtie" class="social-btn" aria-label="Facebook">
          <i data-lucide="facebook"></i>
        </a>
      </div>
    </div>

    <form class="cform r d2" id="contactForm" method="POST" action="{{ route('contact.store') }}">
      @csrf
      <div id="formMessage" class="form-message hidden"></div>
      <input type="text" name="website" class="contact-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
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
        <i data-lucide="send"></i>
      </button>
    </form>
  </div>
</section>

<div class="welcome-modal" id="welcome-modal" aria-hidden="true">
  <div class="welcome-backdrop" data-welcome-close></div>
  <section class="welcome-card" role="dialog" aria-modal="true" aria-labelledby="welcome-title">
    <div class="welcome-kicker">Portfolio Access</div>
    <h2 id="welcome-title" class="welcome-title">Welcome to my portfolio.</h2>
    <p class="welcome-copy">
      I’m Kristian, a web and mobile developer building practical systems, clean interfaces, and focused digital experiences.
    </p>
    <div class="welcome-actions">
      <button type="button" class="welcome-primary" data-welcome-close>Explore Portfolio</button>
      <button type="button" class="welcome-secondary" data-welcome-close>Skip Intro</button>
    </div>
    <blockquote class="welcome-quote">
      “God never said that the journey will be easy, but He did say that the arrival will be worthwhile.”
      <cite>Max Lucado</cite>
    </blockquote>
  </section>
</div>
@endsection
