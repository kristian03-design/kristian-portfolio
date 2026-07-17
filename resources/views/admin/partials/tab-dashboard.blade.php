<section id="tab-dashboard" class="tab active">
    <!-- Stat Cards Strip -->
    <div class="stats-grid">
        <div class="stat-card" data-tab="projects" style="cursor: pointer;">
            <div class="stat-header">
                <span class="stat-title">Projects</span>
                <i class="ti ti-folder-open stat-icon"></i>
            </div>
            <div class="stat-val">{{ str_pad($projects->count(), 2, '0', STR_PAD_LEFT) }}</div>
            <div class="stat-meta">Active portfolio components</div>
        </div>

        <div class="stat-card" data-tab="skills" style="cursor: pointer;">
            <div class="stat-header">
                <span class="stat-title">Skills Catalog</span>
                <i class="ti ti-code-square stat-icon"></i>
            </div>
            <div class="stat-val">{{ str_pad($skills->count(), 2, '0', STR_PAD_LEFT) }}</div>
            <div class="stat-meta"><span>{{ $avgSkill }}%</span> average proficiency</div>
        </div>

        <div class="stat-card" data-tab="experience" style="cursor: pointer;">
            <div class="stat-header">
                <span class="stat-title">Experience</span>
                <i class="ti ti-briefcase stat-icon"></i>
            </div>
            <div class="stat-val">{{ str_pad($experiences->count(), 2, '0', STR_PAD_LEFT) }}</div>
            <div class="stat-meta">Professional milestones</div>
        </div>

        <div class="stat-card" data-tab="messages" style="cursor: pointer;">
            <div class="stat-header">
                <span class="stat-title">Unread Mail</span>
                <i class="ti ti-mail-opened stat-icon {{ $unreadCount > 0 ? 'danger' : '' }}"></i>
            </div>
            <div class="stat-val @if($unreadCount > 0) danger @endif">{{ str_pad($unreadCount, 2, '0', STR_PAD_LEFT) }}</div>
            <div class="stat-meta @if($unreadCount > 0) positive @endif">
                <span>{{ $unreadCount > 0 ? 'Action required' : 'All caught up' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Workspace Grid -->
    <div class="two-col">
        <!-- Left Column: Recent Projects Table -->
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">
                    <i class="ti ti-news"></i> Recent Projects
                </div>
                <button type="button" class="btn btn-ghost btn-sm" data-tab="projects">
                    <i class="ti ti-plus"></i> View All
                </button>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Project Title</th>
                            <th>Stack</th>
                            <th>Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects->take(6) as $project)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-primary);">{{ $project->title }}</div>
                                    <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">{{ Str::limit($project->description, 60) }}</div>
                                </td>
                                <td>
                                    @php
                                        $stack = is_array($project->tech_stack)
                                            ? $project->tech_stack
                                            : array_filter(explode(',', $project->tech_stack ?? ''));
                                    @endphp
                                    <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                        @foreach(array_slice($stack, 0, 3) as $tech)
                                            <span class="tag tag-published" style="font-size: 9px; font-weight: 600;">{{ trim($tech) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td style="font-weight: 600; color: var(--text-secondary);">
                                    {{ str_pad($project->order ?? 0, 2, '0', STR_PAD_LEFT) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="empty-panel">
                                    No projects created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Quick Actions & Top Skills -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Quick Actions -->
            <div class="panel" style="margin-bottom: 0;">
                <div class="panel-head">
                    <div class="panel-title">
                        <i class="ti ti-run"></i> Quick Operations
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; padding: 10px;">
                    <button type="button" class="nav-item" data-tab="projects" style="color: var(--text-primary); text-align: left; padding: 12px; justify-content: space-between; border-radius: var(--radius-sm);">
                        <span style="display: flex; align-items: center; gap: 12px;">
                            <i class="ti ti-folder-plus" style="font-size: 18px; color: var(--primary);"></i>
                            Add new portfolio project
                        </span>
                        <i class="ti ti-chevron-right" style="color: var(--text-muted);"></i>
                    </button>
                    <button type="button" class="nav-item" data-tab="skills" style="color: var(--text-primary); text-align: left; padding: 12px; justify-content: space-between; border-radius: var(--radius-sm);">
                        <span style="display: flex; align-items: center; gap: 12px;">
                            <i class="ti ti-circle-plus" style="font-size: 18px; color: var(--primary);"></i>
                            Add technical skills
                        </span>
                        <i class="ti ti-chevron-right" style="color: var(--text-muted);"></i>
                    </button>
                    <button type="button" class="nav-item" data-tab="experience" style="color: var(--text-primary); text-align: left; padding: 12px; justify-content: space-between; border-radius: var(--radius-sm);">
                        <span style="display: flex; align-items: center; gap: 12px;">
                            <i class="ti ti-calendar-event" style="font-size: 18px; color: var(--primary);"></i>
                            Add experience milestone
                        </span>
                        <i class="ti ti-chevron-right" style="color: var(--text-muted);"></i>
                    </button>
                    <button type="button" class="nav-item" data-tab="certifications" style="color: var(--text-primary); text-align: left; padding: 12px; justify-content: space-between; border-radius: var(--radius-sm);">
                        <span style="display: flex; align-items: center; gap: 12px;">
                            <i class="ti ti-notes" style="font-size: 18px; color: var(--primary);"></i>
                            Upload &amp; OCR scan certification
                        </span>
                        <i class="ti ti-chevron-right" style="color: var(--text-muted);"></i>
                    </button>
                </div>
            </div>

            <!-- Top Skills -->
            @if($skills->count())
                <div class="panel" style="margin-bottom: 0;">
                    <div class="panel-head">
                        <div class="panel-title">
                            <i class="ti ti-stars"></i> Top Skills
                        </div>
                    </div>
                    <div style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
                        @foreach($skills->sortByDesc('proficiency_level')->take(5) as $skill)
                            @php
                                $pct = $skill->proficiency_level;
                            @endphp
                            <div>
                                <div style="display: flex; justify-content: space-between; font-size: 12.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                                    <span>{{ $skill->name }}</span>
                                    <span>{{ $pct }}%</span>
                                </div>
                                <div style="height: 6px; background-color: var(--border-color); border-radius: 4px; overflow: hidden;">
                                    <div style="width: {{ $pct }}%; height: 100%; background-color: var(--primary); border-radius: 4px;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Inbox Preview list -->
    <div class="panel" style="margin-top: 24px;">
        <div class="panel-head">
            <div class="panel-title">
                <i class="ti ti-mail-opened"></i> Recent Inbox Messages
            </div>
            <button type="button" class="btn btn-ghost btn-sm" data-tab="messages">
                Open Inbox
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
                    {{ collect(explode(' ', $msg->name))->map(fn($n) => $n[0] ?? '')->take(2)->join('') }}
                </div>

                <div class="inbox-body">
                    <div class="inbox-header">
                        <span class="inbox-name {{ $isUnread ? '' : 'read' }}">{{ $msg->name }}</span>
                        @if($isUnread)
                            <span class="new-badge">NEW</span>
                        @endif
                    </div>
                    <div class="inbox-email">{{ $msg->email }}</div>
                    <div class="inbox-msg">{{ Str::limit($msg->message, 140) }}</div>
                </div>

                <div class="inbox-meta">
                    <div class="inbox-time">{{ $msg->created_at->diffForHumans() }}</div>
                </div>
            </div>
        @empty
            <div class="empty-panel">
                No inbox messages received yet.
            </div>
        @endforelse
    </div>
</section>
