<section id="tab-projects" class="tab">
    <div class="two-col">

        <!-- Add/Edit Project Form -->
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
                    <input type="text" name="title" class="field" placeholder="Project name" required>
                </div>
                <div class="field-group">
                    <label>Description</label>
                    <textarea name="description" class="field" rows="4" placeholder="Brief overview..." required></textarea>
                </div>
                <div class="field-group">
                    <label>Select Tech Stack</label>
                    @php
                        $defaultTech = ['Laravel', 'PHP', 'Flutter', 'Dart', 'MySQL', 'PostgreSQL', 'HTML', 'CSS', 'JavaScript', 'Tailwind CSS', 'Bootstrap', 'Vue.js', 'React', 'Git', 'Docker', 'Firebase', 'Supabase'];
                        $dbSkills = $skills->pluck('name')->toArray();
                        $allTech = array_unique(array_merge($dbSkills, $defaultTech));
                        sort($allTech);
                    @endphp
                    <div style="display: flex; flex-wrap: wrap; gap: 6px; background: var(--bg-base); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); max-height: 180px; overflow-y: auto;">
                        @foreach($allTech as $tech)
                            <label style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 6px; font-size: 12px; cursor: pointer; user-select: none;">
                                <input type="checkbox" name="tech_stack[]" value="{{ $tech }}" class="tech-checkbox" style="accent-color: var(--primary);">
                                <span>{{ $tech }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="field-group">
                    <label>Custom / Other Tech Stack <span class="label-note">(comma-separated)</span></label>
                    <input type="text" name="custom_tech_stack" class="field" placeholder="e.g. GraphQL, AWS, Redis">
                </div>
                <div class="field-group">
                    <label>Demo URL</label>
                    <input type="url" name="url" class="field" placeholder="https://...">
                </div>
                <div class="field-group">
                    <label>GitHub URL</label>
                    <input type="url" name="github_url" class="field" placeholder="https://github.com/...">
                </div>
                <div class="field-group">
                    <label>Preview Image</label>
                    <input type="file" name="image" class="field" accept="image/*">
                    <div class="field-hint hidden" id="project-image-hint">Leave empty to keep the current preview image.</div>
                </div>
                <button type="submit" class="btn btn-primary full-width" id="project-submit">
                    <i class="ti ti-device-floppy"></i> <span>Save Project</span>
                </button>
            </div>
        </form>

        <!-- Project List -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
            @forelse($projects as $project)
                @php
                    $stack = is_array($project->tech_stack)
                        ? $project->tech_stack
                        : array_filter(explode(',', $project->tech_stack ?? ''));
                @endphp
                <article class="panel" id="project-card-{{ $project->id }}" style="margin-bottom: 0; padding: 20px; display: flex; justify-content: space-between; gap: 20px; align-items: flex-start; position: relative;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 6px; font-family: var(--font-display); font-size: 15px; font-weight: 700; color: var(--text-primary);">
                            {{ $project->title }}
                            @if(($project->status ?? 'Draft') === 'Draft')
                                <span class="badge badge-draft" style="font-size: 9px; padding: 2px 6px;">Draft</span>
                            @else
                                <span class="badge badge-published" style="font-size: 9px; padding: 2px 6px;">Published</span>
                            @endif
                            
                            @if(($project->documentation_status ?? 'under_development') === 'published')
                                <span class="badge badge-published" style="font-size: 9px; padding: 2px 6px;">🟢 Doc</span>
                            @else
                                <span class="badge badge-draft" style="font-size: 9px; padding: 2px 6px;">🟡 Coming Soon</span>
                            @endif
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 8px;">
                            @foreach($stack as $tech)
                                <span class="tag tag-published" style="font-size: 10px; font-weight: 600;">{{ trim($tech) }}</span>
                            @endforeach
                        </div>
                        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 12px; line-height: 1.5; white-space: pre-wrap;">{{ $project->description }}</p>
                        @if($project->url || $project->github_url)
                            <div style="display: flex; gap: 8px; margin-top: 16px;">
                                @if($project->url)
                                    <a href="{{ $project->url }}" target="_blank" class="btn btn-ghost btn-sm">
                                        <i class="ti ti-external-link"></i> Live Demo
                                    </a>
                                @endif
                                @if($project->github_url)
                                    <a href="{{ $project->github_url }}" target="_blank" class="btn btn-ghost btn-sm">
                                        <i class="ti ti-brand-github"></i> Repository
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px; flex-shrink: 0; align-items: stretch;">
                        <button type="button" class="btn btn-ghost btn-sm project-edit-btn" data-project-id="{{ $project->id }}">
                            <i class="ti ti-edit"></i> Edit Basic
                        </button>
                        <button type="button" class="btn btn-ghost btn-sm project-details-btn" data-project-id="{{ $project->id }}" title="Edit case study details">
                            <i class="ti ti-layout-list"></i> Details Drawer
                        </button>
                        <form method="POST" action="/admin/projects/{{ $project->id }}" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm full-width" data-confirm="Are you sure you want to delete this project?">
                                <i class="ti ti-trash"></i> Delete
                            </button>
                        </form>
                    </div>

                    <!-- Serialized JSON block for JS parsing -->
                    <script type="application/json" id="project-data-{{ $project->id }}">{!! json_encode([
                        'id'               => $project->id,
                        'title'            => $project->title,
                        'description'      => $project->description,
                        'url'              => $project->url,
                        'github_url'       => $project->github_url,
                        'tech_stack'       => array_values($stack),
                        'image_path'       => $project->image_path,
                        'slug'             => $project->slug,
                        'category'         => $project->category,
                        'status'           => $project->status,
                        'duration'         => $project->duration,
                        'role'             => $project->role,
                        'documentation_url'=> $project->documentation_url,
                        'video_demo_url'   => $project->video_demo_url,
                        'documentation_status'=> $project->documentation_status ?? 'under_development',
                        'metrics'          => $project->metrics,
                        'overview'         => $project->overview,
                        'gallery'          => $project->gallery,
                        'features'         => $project->features,
                        'architecture'     => $project->architecture,
                        'challenges'       => $project->challenges,
                        'timeline'         => $project->timeline,
                        'performance'      => $project->performance,
                        'security_details' => $project->security_details,
                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
                </article>
            @empty
                <div class="panel empty-panel">
                    No projects found. Use the form on the left to add your first project.
                </div>
            @endforelse
        </div>

    </div>
</section>
