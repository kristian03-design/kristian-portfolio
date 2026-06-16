<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | {{ config('app.name', 'Portfolio') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/chibi-logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:ital,wght@0,300;0,400;0,500;1,400&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body>

@php
    $unreadCount  = $unreadCount ?? $messages->where('status', 'unread')->count();
    $avgSkill     = $skills->count() ? round($skills->avg('proficiency_level')) : 0;
    $latestMsg    = $latestMsg ?? $messages->first();

    $initials = fn($name) => collect(explode(' ', $name))
        ->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
@endphp

<div class="layout">

    {{-- --------- SIDEBAR --------------------------------------------------------------------------------------------------------------------------- --}}
    <aside class="sidebar">
        <div class="brand-block">
            <div class="brand-label">Portfolio CMS</div>
            <div class="brand-name">CTRL<span>.</span></div>
            <div class="brand-sub">
                <span class="status-dot"></span>
                online &middot; admin
            </div>
        </div>

        <nav class="nav">
            <div class="nav-section">Workspace</div>

            <button type="button" class="nav-item active" data-tab="dashboard">
                <i class="ti ti-layout-dashboard"></i>Dashboard
            </button>
            <button type="button" class="nav-item" data-tab="projects">
                <i class="ti ti-folder"></i>Projects
            </button>
            <button type="button" class="nav-item" data-tab="skills">
                <i class="ti ti-cpu"></i>Skills
            </button>
            <button type="button" class="nav-item" data-tab="experience">
                <i class="ti ti-briefcase"></i>Experience
            </button>
            <button type="button" class="nav-item" data-tab="certifications">
                <i class="ti ti-certificate"></i>Certifications
            </button>

            <div class="nav-section">Comms</div>

            <button type="button" class="nav-item" data-tab="messages">
                <i class="ti ti-mail"></i>Messages
                @if($unreadCount > 0)
                    <span class="badge-pill">{{ $unreadCount }}</span>
                @endif
            </button>
        </nav>

        <div class="sidebar-bottom">
            <a href="/" target="_blank" class="side-btn">
                <i class="ti ti-external-link"></i>View portfolio
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="side-btn danger full-width">
                    <i class="ti ti-logout"></i>Sign out
                </button>
            </form>
        </div>
    </aside>

    {{-- --------- MAIN ------------------------------------------------------------------------------------------------------------------------------------ --}}
    <main class="main">
        <header class="topbar">
            <div class="topbar-left">
                <span class="page-crumb">Admin</span>
                <span class="page-title" id="page-title">Dashboard</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-chip">
                    <i class="ti ti-mail"></i>
                    @if($latestMsg)
                        <span>Latest: <strong>{{ $latestMsg->name }}</strong>
                        &mdash; {{ $latestMsg->created_at->diffForHumans() }}</span>
                    @else
                        <span>No messages yet</span>
                    @endif
                </div>
            </div>
        </header>

        <div class="content">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="ti ti-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-error">
                    <i class="ti ti-alert-triangle"></i>
                    {{ session('warning') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- ------------------------------------------------------------------------------------------------------------------------------------------------------
                 DASHBOARD TAB
            --------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            <section id="tab-dashboard" class="tab active">

                {{-- Stat strip --}}
                <div class="stat-strip">
                    <div class="stat-cell">
                        <div class="stat-label">
                            <i class="ti ti-folder"></i>Projects
                        </div>
                        <div class="stat-value accent">
                            {{ str_pad($projects->count(), 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="stat-sub">total portfolio items</div>
                    </div>
                    <div class="stat-cell">
                        <div class="stat-label">
                            <i class="ti ti-cpu"></i>Skills
                        </div>
                        <div class="stat-value">
                            {{ str_pad($skills->count(), 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="stat-sub">{{ $avgSkill }}% avg. proficiency</div>
                    </div>
                    <div class="stat-cell">
                        <div class="stat-label">
                            <i class="ti ti-briefcase"></i>Experience
                        </div>
                        <div class="stat-value">
                            {{ str_pad($experiences->count(), 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="stat-sub">roles on record</div>
                    </div>
                    <div class="stat-cell">
                        <div class="stat-label">
                            <i class="ti ti-inbox danger"></i>Unread
                        </div>
                        <div class="stat-value {{ $unreadCount > 0 ? 'danger' : '' }}">
                            {{ str_pad($unreadCount, 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="stat-sub">
                            {{ $unreadCount > 0 ? 'needs review' : 'all caught up' }}
                        </div>
                    </div>
                </div>

                {{-- Main grid --}}
                <div class="main-grid">

                    {{-- Recent projects table --}}
                    <div class="panel">
                        <div class="panel-head">
                            <div class="panel-title">
                                <i class="ti ti-folder-open"></i>Recent Projects
                            </div>
                            <button type="button" class="btn btn-ghost btn-sm" data-tab="projects">
                                <i class="ti ti-plus"></i>Add
                            </button>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Stack</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projects->take(6) as $project)
                                    <tr>
                                        <td>
                                            <div class="td-title">{{ $project->title }}</div>
                                            <div class="td-sub">{{ Str::limit($project->description, 70) }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $stack = is_array($project->tech_stack)
                                                    ? $project->tech_stack
                                                    : array_filter(explode(',', $project->tech_stack ?? ''));
                                            @endphp
                                            @foreach(array_slice($stack, 0, 2) as $tech)
                                                <span class="tag">{{ trim($tech) }}</span>
                                            @endforeach
                                        </td>
                                        <td class="muted-cell">
                                            {{ str_pad($project->order ?? 0, 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="empty-cell">
                                            No projects yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Right column --}}
                    <div class="right-col">

                        {{-- Quick actions --}}
                        <div class="panel">
                            <div class="panel-head">
                                <div class="panel-title">
                                    <i class="ti ti-bolt"></i>Quick Actions
                                </div>
                            </div>
                            <div class="action-list">
                                <button type="button" class="action-item" data-tab="projects">
                                    <i class="ti ti-folder-plus"></i>
                                    Add portfolio project
                                    <i class="ti ti-arrow-right action-arrow"></i>
                                </button>
                                <button type="button" class="action-item" data-tab="skills">
                                    <i class="ti ti-plus-circle"></i>
                                    Add new skill
                                    <i class="ti ti-arrow-right action-arrow"></i>
                                </button>
                                <button type="button" class="action-item" data-tab="experience">
                                    <i class="ti ti-calendar-plus"></i>
                                    Update experience
                                    <i class="ti ti-arrow-right action-arrow"></i>
                                </button>
                                <button type="button" class="action-item" data-tab="certifications">
                                    <i class="ti ti-certificate"></i>
                                    Add certification
                                    <i class="ti ti-arrow-right action-arrow"></i>
                                </button>
                                <button type="button" class="action-item" data-tab="messages">
                                    <i class="ti ti-mail-opened"></i>
                                    Review messages
                                    <i class="ti ti-arrow-right action-arrow"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Skills mini --}}
                        @if($skills->count())
                        <div class="panel">
                            <div class="panel-head">
                                <div class="panel-title">
                                    <i class="ti ti-chart-bar"></i>Top Skills
                                </div>
                            </div>
                            <div class="top-skills-list">
                                @foreach($skills->sortByDesc('proficiency_level')->take(5) as $skill)
                                    @php
                                        $pct = $skill->proficiency_level;
                                        $color = $pct >= 80 ? 'var(--accent)' : ($pct >= 60 ? 'var(--accent2)' : 'var(--warn)');
                                    @endphp
                                    <div class="skill-bar-row">
                                        <span class="skill-name">{{ Str::limit($skill->name, 12) }}</span>
                                        <div class="skill-track">
                                            <div class="skill-fill js-skill-fill"
                                                 data-level="{{ $pct }}"
                                                 data-color="{{ $color }}">
                                            </div>
                                        </div>
                                        <span class="skill-pct js-skill-pct" data-color="{{ $color }}">
                                            {{ $pct }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                {{-- Inbox preview --}}
                <div class="panel">
                    <div class="panel-head">
                        <div class="panel-title">
                            <i class="ti ti-inbox"></i>Inbox Preview
                        </div>
                        <button type="button" class="btn btn-ghost btn-sm" data-tab="messages">
                            View all
                        </button>
                    </div>

                    @forelse($messages->take(4) as $msg)
                        @php $isUnread = $msg->status === 'unread'; @endphp
                        <div class="inbox-row {{ $isUnread ? 'unread' : '' }}">
                            @if($isUnread)
                                <div class="unread-dot"></div>
                            @else
                                <div class="unread-spacer"></div>
                            @endif

                            <div class="inbox-avatar {{ $isUnread ? '' : 'read' }}">
                                {{ $initials($msg->name) }}
                            </div>

                            <div class="inbox-body">
                                <div class="inbox-header">
                                    <span class="inbox-name {{ $isUnread ? '' : 'read' }}">
                                        {{ $msg->name }}
                                    </span>
                                    @if($isUnread)
                                        <span class="new-badge">NEW</span>
                                    @endif
                                </div>
                                <div class="inbox-email">{{ $msg->email }}</div>
                                <div class="inbox-msg">{{ Str::limit($msg->message, 100) }}</div>
                            </div>

                            <div class="inbox-time">{{ $msg->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="empty-panel-sm">
                            Inbox is empty.
                        </div>
                    @endforelse
                </div>

            </section>

            {{-- ------------------------------------------------------------------------------------------------------------------------------------------------------
                            Inbox is empty.
                        </div>
                    @endforelse
                </div>

            </section>

            {{-- ------------------------------------------------------------------------------------------------------------------------------------------------------
                 PROJECTS TAB
            --------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            <section id="tab-projects" class="tab">
                <div class="two-col">

                    {{-- Add project form --}}
                    <form method="POST"
                          action="/admin/projects"
                          enctype="multipart/form-data"
                          class="panel"
                          id="project-form"
                          data-create-action="/admin/projects">
                        @csrf
                        <input type="hidden" name="_method" id="project-form-method" value="POST" disabled>
                        <div class="panel-head">
                            <div class="panel-title">
                                <i class="ti ti-folder-plus" id="project-form-icon"></i>
                                <span id="project-form-title">New Project</span>
                            </div>
                            <button type="button" class="btn btn-ghost btn-sm hidden" id="project-edit-cancel">
                                <i class="ti ti-x"></i>Cancel
                            </button>
                        </div>
                        <div class="form-body">
                            <div class="field-group">
                                <label>Title</label>
                                <input type="text" name="title" class="field"
                                       placeholder="Project name" required>
                            </div>
                            <div class="field-group">
                                <label>Description</label>
                                <textarea name="description" class="field" rows="4"
                                          placeholder="Brief overview..." required></textarea>
                            </div>
                            <div class="field-group">
                                <label>Select Tech Stack</label>
                                @php
                                    $defaultTech = ['Laravel', 'PHP', 'Flutter', 'Dart', 'MySQL', 'PostgreSQL', 'HTML', 'CSS', 'JavaScript', 'Tailwind CSS', 'Bootstrap', 'Vue.js', 'React', 'Git', 'Docker', 'Firebase', 'Supabase'];
                                    $dbSkills = $skills->pluck('name')->toArray();
                                    $allTech = array_unique(array_merge($dbSkills, $defaultTech));
                                    sort($allTech);
                                @endphp
                                <div class="tech-tags-container">
                                    @foreach($allTech as $tech)
                                        <label class="tech-tag-label">
                                            <input type="checkbox" name="tech_stack[]" value="{{ $tech }}" class="tech-checkbox">
                                            <span class="tech-tag-btn">{{ $tech }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="field-group">
                                <label>Custom / Other Tech Stack <span class="label-note">(comma-separated)</span></label>
                                <input type="text" name="custom_tech_stack" class="field"
                                       placeholder="e.g. GraphQL, AWS, Redis">
                            </div>
                            <div class="field-group">
                                <label>Demo URL</label>
                                <input type="url" name="url" class="field"
                                       placeholder="https://...">
                            </div>
                            <div class="field-group">
                                <label>GitHub URL</label>
                                <input type="url" name="github_url" class="field"
                                       placeholder="https://github.com/...">
                            </div>
                            <div class="field-group">
                                <label>Preview Image</label>
                                <input type="file" name="image" class="field" accept="image/*">
                                <div class="form-hint hidden" id="project-image-hint">Leave empty to keep the current preview image.</div>
                            </div>
                            <button type="submit" class="btn btn-primary full-width" id="project-submit">
                                <i class="ti ti-device-floppy"></i><span>Save Project</span>
                            </button>
                        </div>
                    </form>

                    {{-- Project list --}}
                    <div class="project-list">
                        @forelse($projects as $project)
                            @php
                                $stack = is_array($project->tech_stack)
                                    ? $project->tech_stack
                                    : array_filter(explode(',', $project->tech_stack ?? ''));
                            @endphp
                            <article class="project-card" id="project-card-{{ $project->id }}">
                                <div class="min-w-0">
                                    <div class="proj-title">{{ $project->title }}</div>
                                    <div class="proj-tags">
                                        @foreach($stack as $tech)
                                            <span class="tag">{{ trim($tech) }}</span>
                                        @endforeach
                                    </div>
                                    <div class="proj-desc">{{ $project->description }}</div>
                                    @if($project->url || $project->github_url)
                                        <div class="project-actions">
                                            @if($project->url)
                                                <a href="{{ $project->url }}" target="_blank"
                                                   class="btn btn-ghost btn-sm">
                                                    <i class="ti ti-external-link"></i>Demo
                                                </a>
                                            @endif
                                            @if($project->github_url)
                                                <a href="{{ $project->github_url }}" target="_blank"
                                                   class="btn btn-ghost btn-sm">
                                                    <i class="ti ti-brand-github"></i>GitHub
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="project-card-controls shrink-0">
                                    <button type="button"
                                            class="btn btn-ghost btn-sm project-edit-btn"
                                            data-project-id="{{ $project->id }}">
                                        <i class="ti ti-edit"></i>Edit
                                    </button>
                                    <form method="POST" action="/admin/projects/{{ $project->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                data-confirm="Delete this project?">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <script type="application/json" id="project-data-{{ $project->id }}">{!! json_encode([
                                    'id' => $project->id,
                                    'title' => $project->title,
                                    'description' => $project->description,
                                    'url' => $project->url,
                                    'github_url' => $project->github_url,
                                    'tech_stack' => array_values($stack),
                                    'image_path' => $project->image_path,
                                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
                            </article>
                        @empty
                            <div class="empty-panel">
                                No projects yet.
                            </div>
                        @endforelse
                    </div>

                </div>
            </section>

            {{-- ------------------------------------------------------------------------------------------------------------------------------------------------------
                 SKILLS TAB
            --------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            <section id="tab-skills" class="tab">

                {{-- Add skill form --}}
                <form method="POST" action="/admin/skills" class="panel panel-spaced">
                    @csrf
                    @php
                        $existingSkillNames = $skills->pluck('name')
                            ->map(fn($name) => strtolower($name))
                            ->all();
                    @endphp
                    <div class="skills-form-row">
                        <div class="field-group">
                            <label>Select Skills</label>
                            <div class="tech-tags-container skill-tags-container">
                                @foreach($skillCatalog as $category => $catalogSkills)
                                    @foreach($catalogSkills as $catalogSkill)
                                        @php
                                            $isExistingSkill = in_array(strtolower($catalogSkill), $existingSkillNames, true);
                                        @endphp
                                        <label class="tech-tag-label {{ $isExistingSkill ? 'is-disabled' : '' }}"
                                               title="{{ $isExistingSkill ? 'Already added' : $category }}">
                                            <input type="checkbox"
                                                   name="names[]"
                                                   value="{{ $catalogSkill }}"
                                                   class="tech-checkbox"
                                                   @disabled($isExistingSkill)>
                                            <span class="tech-tag-btn">{{ $catalogSkill }}</span>
                                        </label>
                                    @endforeach
                                @endforeach
                            </div>
                            <label>Custom / Other Skills <span class="label-note">(comma-separated)</span></label>
                            <input type="text" name="custom_skills" class="field"
                                   placeholder="e.g. GraphQL, AWS, Redis">
                        </div>
                        <div class="field-group">
                            <label>Custom Category</label>
                            <select name="custom_category" class="field">
                                <option value="Tools">Tools</option>
                                <option value="Frontend">Frontend</option>
                                <option value="Backend">Backend</option>
                                <option value="Mobile">Mobile</option>
                            </select>
                        </div>
                        <div class="field-group narrow">
                            <label>Level %</label>
                            <input type="number" name="proficiency_level" class="field"
                                   value="80" min="0" max="100" required>
                        </div>
                        <button type="submit" class="btn btn-primary shrink-0">
                            <i class="ti ti-plus"></i>Add Skill
                        </button>
                    </div>
                </form>

                {{-- Skills table --}}
                <div class="panel">
                    <table>
                        <thead>
                            <tr>
                                <th>Skill</th>
                                <th>Category</th>
                                <th>Proficiency</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($skills->sortByDesc('proficiency_level') as $skill)
                                @php
                                    $pct   = $skill->proficiency_level;
                                    $color = $pct >= 80 ? 'var(--accent)' : ($pct >= 60 ? 'var(--accent2)' : 'var(--warn)');
                                    $catClass = match(strtolower($skill->category)) {
                                        'backend' => 'backend',
                                        'tools'   => 'tools',
                                        default   => '',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="td-title">{{ $skill->name }}</div>
                                    </td>
                                    <td>
                                        <span class="tag {{ $catClass }}">{{ $skill->category }}</span>
                                    </td>
                                    <td>
                                        <div class="skill-table-progress">
                                            <div class="skill-track skill-track-fixed">
                                                <div class="skill-fill js-skill-fill"
                                                     data-level="{{ $pct }}"
                                                     data-color="{{ $color }}">
                                                </div>
                                            </div>
                                            <span class="skill-table-pct js-skill-pct" data-color="{{ $color }}">
                                                {{ $pct }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <form method="POST" action="/admin/skills/{{ $skill->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    data-confirm="Delete this skill?">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="empty-cell">
                                        No skills yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </section>

            {{-- ------------------------------------------------------------------------------------------------------------------------------------------------------
                 EXPERIENCE TAB
            --------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            <section id="tab-experience" class="tab">
                <div class="two-col">

                    {{-- Add experience form --}}
                    <form method="POST" action="/admin/experiences" class="panel">
                        @csrf
                        <div class="panel-head">
                            <div class="panel-title">
                                <i class="ti ti-calendar-plus"></i>Add Experience
                            </div>
                        </div>
                        <div class="form-body">
                            <div class="field-group">
                                <label>Role</label>
                                <input type="text" name="role" class="field"
                                       placeholder="Senior Developer" required>
                            </div>
                            <div class="field-group">
                                <label>Company</label>
                                <input type="text" name="company" class="field"
                                       placeholder="Acme Corp" required>
                            </div>
                            <div class="field-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="field" required>
                            </div>
                            <div class="field-group">
                                <label>End Date <span class="label-note">(leave blank if current)</span></label>
                                <input type="date" name="end_date" class="field">
                            </div>
                            <div class="field-group">
                                <label>Description</label>
                                <textarea name="description" class="field" rows="4"
                                          placeholder="Key responsibilities and achievements..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary full-width">
                                <i class="ti ti-device-floppy"></i>Save Experience
                            </button>
                        </div>
                    </form>

                    {{-- Experience list --}}
                    <div class="exp-list">
                        @forelse($experiences as $exp)
                            <article class="exp-card">
                                <div class="exp-card-head">
                                    <div>
                                        <div class="exp-dates">
                                            {{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }}
                                            &mdash;
                                            {{ $exp->is_current || !$exp->end_date
                                                ? 'PRESENT'
                                                : strtoupper(\Carbon\Carbon::parse($exp->end_date)->format('M Y')) }}
                                        </div>
                                        <div class="exp-role">{{ $exp->role }}</div>
                                        <div class="exp-company">{{ $exp->company }}</div>
                                    </div>
                                    <form method="POST" action="/admin/experiences/{{ $exp->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                data-confirm="Delete this experience?">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @if($exp->description)
                                    <div class="exp-desc">{{ $exp->description }}</div>
                                @endif
                            </article>
                        @empty
                            <div class="empty-panel">
                                No experience entries yet.
                            </div>
                        @endforelse
                    </div>

                </div>
            </section>

            {{-- ------------------------------------------------------------------------------------------------------------------------------------------------------
                 CERTIFICATIONS TAB
            --------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            <section id="tab-certifications" class="tab">
                {{-- Certificate Auto-Fill Upload Form --}}
                <form method="POST" action="/admin/certifications" enctype="multipart/form-data" class="ocr-form-container" style="margin-bottom: 24px;">
                    @csrf
                    <div class="cert-form-grid">
                        
                        {{-- Left Column: Certificate Image Upload & Preview --}}
                        <div class="panel cert-upload-panel">
                            <div class="panel-head">
                                <div class="panel-title">
                                    <i class="ti ti-scan"></i> Certificate Auto-Fill Upload
                                </div>
                            </div>
                            <div class="form-body">
                                {{-- Upload Box --}}
                                <div class="ocr-upload-box" id="ocr-dropzone">
                                    <i class="ti ti-cloud-upload ocr-upload-icon"></i>
                                    <p class="ocr-upload-text">Drag & drop certificate image or PDF here or click to browse</p>
                                    <p class="ocr-upload-subtext">Supports PNG, JPG, JPEG, PDF (Max 4MB)</p>
                                    <input type="file" name="certificate_image" id="cert-image-ocr" accept="image/*,application/pdf" class="ocr-file-input">
                                </div>

                                {{-- Image Preview Section --}}
                                <div class="ocr-preview-container" id="ocr-preview-container" style="display: none;">
                                    <div class="ocr-preview-title">Image Preview</div>
                                    <img id="cert-preview" src="#" alt="Certificate Preview" class="ocr-preview-image">
                                    <button type="button" class="btn btn-danger btn-sm" id="btn-remove-preview" style="margin-top: 10px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="ti ti-trash"></i> Remove Image
                                    </button>
                                </div>

                                {{-- Loading State --}}
                                <div class="ocr-loading-state" id="ocr-loading-state" style="display: none;">
                                    <span class="spinner" style="border-width: 3px; width: 20px; height: 20px; border-top-color: transparent;"></span>
                                    <div class="ocr-loading-text">Scanning Certificate...</div>
                                    <div class="ocr-progress-bar-container">
                                        <div class="ocr-progress-bar" id="ocr-progress-bar" style="width: 0%;"></div>
                                    </div>
                                    <div class="ocr-progress-percent" id="ocr-progress-percent">0%</div>
                                </div>

                                {{-- Validation Warnings --}}
                                <div class="ocr-validation-warning" id="ocr-validation-warning" style="display: none;">
                                    <i class="ti ti-alert-triangle" style="font-size: 18px;"></i>
                                    <div>
                                        <strong>Validation Notice:</strong> Some fields could not be automatically detected. Please check the fields on the right and fill them manually.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Auto-filled Details Form --}}
                        <div class="panel">
                            <div class="panel-head">
                                <div class="panel-title">
                                    <i class="ti ti-edit"></i> Certificate Information
                                </div>
                            </div>
                            <div class="form-body">
                                <div class="field-group">
                                    <label>Certificate Title <span class="required-star">*</span></label>
                                    <input type="text" name="title" id="cert_title" class="field"
                                           placeholder="e.g. Google IT Support Professional Certificate" required>
                                </div>
                                <div class="field-group">
                                    <label>Recipient / Student Name</label>
                                    <input type="text" name="recipient_name" id="cert_recipient_name" class="field"
                                           placeholder="e.g. Kristian Hernandez">
                                </div>
                                <div class="field-group">
                                    <label>Program / Course Name</label>
                                    <input type="text" name="course_name" id="cert_course_name" class="field"
                                           placeholder="e.g. Technical Support Fundamentals">
                                </div>
                                <div class="field-group">
                                    <label>Issuing Office / Organization <span class="required-star">*</span></label>
                                    <input type="text" name="issuer" id="cert_issuer" class="field"
                                           placeholder="e.g. Coursera / Google" required>
                                </div>
                                <div class="field-group">
                                    <label>Date Issued <span class="required-star">*</span></label>
                                    <input type="date" name="issue_date" id="cert_issue_date" class="field" required>
                                </div>
                                <div class="field-group">
                                    <label>Expiration Date <span class="label-note">(leave blank if no expiration)</span></label>
                                    <input type="date" name="expiration_date" id="cert_expiration_date" class="field">
                                </div>
                                <div class="field-group">
                                    <label>Certificate Number / Reference Number <span class="label-note">(optional)</span></label>
                                    <input type="text" name="credential_id" id="cert_credential_id" class="field"
                                           placeholder="e.g. ABC123XYZ">
                                </div>
                                <div class="field-group">
                                    <label>Credential Verification URL <span class="label-note">(optional)</span></label>
                                    <input type="url" name="credential_url" id="cert_credential_url" class="field"
                                           placeholder="https://coursera.org/verify/...">
                                </div>
                                <div class="field-group">
                                    <label>Description / Remarks <span class="label-note">(optional)</span></label>
                                    <textarea name="description" id="cert_description" class="field" rows="4"
                                              placeholder="Brief description of the topics covered, skills learned, or projects completed..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary full-width" style="margin-top: 10px;">
                                    <i class="ti ti-device-floppy"></i> Save Certification
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

                {{-- Existing Certifications --}}
                <div class="panel">
                    <div class="panel-head">
                        <div class="panel-title">
                            <i class="ti ti-list"></i> Existing Certifications
                        </div>
                    </div>
                    <div class="form-body">
                        <div class="ocr-existing-grid">
                            @forelse($certifications as $cert)
                                @php
                                    $certPath = $cert->image_path ? ltrim($cert->image_path, '/') : null;
                                    $certUrl = $certPath ? asset($certPath) : null;
                                    $isPdfCert = $certPath && strtolower(pathinfo($certPath, PATHINFO_EXTENSION)) === 'pdf';
                                @endphp
                                <article class="ocr-cert-card">
                                    <div class="ocr-cert-card-img-container">
                                        @if($certUrl && $isPdfCert)
                                            <a href="{{ $certUrl }}" target="_blank" rel="noopener" class="ocr-cert-card-file">
                                                <i class="ti ti-file-type-pdf"></i>
                                                <span>Open PDF</span>
                                            </a>
                                        @elseif($certUrl)
                                            <img src="{{ $certUrl }}" alt="{{ $cert->title }}" class="ocr-cert-card-img">
                                        @else
                                            <div class="ocr-cert-card-img-placeholder">
                                                <i class="ti ti-image-off"></i>
                                                <span>No Image</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ocr-cert-card-content">
                                        <div class="ocr-cert-card-date">
                                            {{ \Carbon\Carbon::parse($cert->issue_date)->format('M Y') }}
                                            @if($cert->expiration_date)
                                                &mdash; {{ \Carbon\Carbon::parse($cert->expiration_date)->format('M Y') }}
                                            @else
                                                &mdash; NO EXPIRY
                                            @endif
                                        </div>
                                        <div class="ocr-cert-card-title">{{ $cert->title }}</div>
                                        <div class="ocr-cert-card-detail"><strong>Issuer:</strong> {{ $cert->issuer }}</div>
                                        @if($cert->recipient_name)
                                            <div class="ocr-cert-card-detail"><strong>Recipient:</strong> {{ $cert->recipient_name }}</div>
                                        @endif
                                        @if($cert->course_name)
                                            <div class="ocr-cert-card-detail"><strong>Course:</strong> {{ $cert->course_name }}</div>
                                        @endif
                                        @if($cert->credential_id)
                                            <div class="ocr-cert-card-detail"><strong>ID:</strong> <span class="mono">{{ $cert->credential_id }}</span></div>
                                        @endif
                                        @if($cert->description)
                                            <p class="ocr-cert-card-desc">{{ Str::limit($cert->description, 100) }}</p>
                                        @endif
                                    </div>
                                    <div class="ocr-cert-card-actions">
                                        @if($cert->credential_url)
                                            <a href="{{ $cert->credential_url }}" target="_blank" class="btn btn-ghost btn-sm" title="Verify Link">
                                                <i class="ti ti-external-link"></i>
                                            </a>
                                        @endif
                                        <form method="POST" action="/admin/certifications/{{ $cert->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this certification?">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </article>
                            @empty
                                <div class="empty-panel">
                                    No certifications yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            {{-- ------------------------------------------------------------------------------------------------------------------------------------------------------
                 MESSAGES TAB
            --------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            <section id="tab-messages" class="tab">

                <div class="panel panel-spaced">
                    <div class="panel-head">
                        <div class="panel-title">
                            <i class="ti ti-inbox {{ $unreadCount > 0 ? 'danger' : '' }}"></i>
                            Inbox
                            @if($unreadCount > 0)
                                <span class="unread-count-text">
                                    &mdash; {{ $unreadCount }} unread
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="panel">
                    @forelse($messages as $msg)
                        @php $isUnread = $msg->status === 'unread'; @endphp
                        <div class="inbox-row {{ $isUnread ? 'unread' : '' }}">
                            @if($isUnread)
                                <div class="unread-dot"></div>
                            @else
                                <div class="unread-spacer"></div>
                            @endif

                            <div class="inbox-avatar {{ $isUnread ? '' : 'read' }}">
                                {{ $initials($msg->name) }}
                            </div>

                            <div class="inbox-body">
                                <div class="inbox-header">
                                    <span class="inbox-name {{ $isUnread ? '' : 'read' }}">
                                        {{ $msg->name }}
                                    </span>
                                    @if($isUnread)
                                        <span class="new-badge">NEW</span>
                                    @endif
                                </div>
                                <div class="inbox-email">
                                    <a href="mailto:{{ $msg->email }}">{{ $msg->email }}</a>
                                </div>
                                <div class="inbox-msg">{{ $msg->message }}</div>
                            </div>

                            <div class="inbox-meta">
                                <div class="inbox-time">{{ $msg->created_at->diffForHumans() }}</div>
                                @if($isUnread)
                                    <form method="POST" action="/admin/messages/{{ $msg->id }}/read">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-ghost btn-sm">
                                            <i class="ti ti-check"></i>Mark read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-panel-lg">
                            Inbox is empty.
                        </div>
                    @endforelse
                </div>

            </section>

        </div>{{-- /content --}}
    </main>
</div>

<div id="toast-container" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px;"></div>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.showToast) {
                window.showToast("{{ session('success') }}", 'success');
            }
        });
    </script>
@endif

</body>
</html>
