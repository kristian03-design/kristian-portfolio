<section id="tab-skills" class="tab">

    <!-- Add Skills Panel -->
    <form method="POST" action="/admin/skills" class="panel">
        @csrf
        @php
            $existingSkillNames = $skills->pluck('name')
                ->map(fn($name) => strtolower($name))
                ->all();
        @endphp
        <div class="panel-head">
            <div class="panel-title">
                <i class="ti ti-circle-plus"></i> Add Skills to Catalog
            </div>
        </div>
        <div class="form-body">
            <div class="field-group">
                <label>Select Skills from Catalog</label>
                <div style="display: flex; flex-wrap: wrap; gap: 6px; background: var(--bg-base); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); max-height: 180px; overflow-y: auto;">
                    @foreach($skillCatalog as $category => $catalogSkills)
                        @foreach($catalogSkills as $catalogSkill)
                            @php
                                $isExistingSkill = in_array(strtolower($catalogSkill), $existingSkillNames, true);
                            @endphp
                            <label style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 6px; font-size: 12px; cursor: pointer; user-select: none; @if($isExistingSkill) opacity: 0.5; cursor: not-allowed; @endif" title="{{ $isExistingSkill ? 'Already added' : $category }}">
                                <input type="checkbox" name="names[]" value="{{ $catalogSkill }}" class="tech-checkbox" style="accent-color: var(--primary);" @disabled($isExistingSkill)>
                                <span>{{ $catalogSkill }}</span>
                            </label>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <div class="d-grid-2" style="margin-top: 16px;">
                <div class="field-group">
                    <label>Custom Skill Name <span class="label-note">(comma-separated for multiples)</span></label>
                    <input type="text" name="custom_skills" class="field" placeholder="e.g. GraphQL, AWS, Redis">
                </div>
                <div class="field-group">
                    <label>Category</label>
                    <select name="custom_category" class="field">
                        <option value="Frontend">Frontend</option>
                        <option value="Backend">Backend</option>
                        <option value="Mobile">Mobile</option>
                        <option value="Tools">Tools</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Proficiency Level %</label>
                    <input type="number" name="proficiency_level" class="field" value="80" min="0" max="100" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">
                <i class="ti ti-plus"></i> Add Skill
            </button>
        </div>
    </form>

    <!-- Skills Listing Panel -->
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title">
                <i class="ti ti-list"></i> Skill Proficiency Index
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Skill Name</th>
                        <th>Category</th>
                        <th style="width: 50%;">Proficiency</th>
                        <th style="width: 80px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skills->sortByDesc('proficiency_level') as $skill)
                        @php
                            $pct = $skill->proficiency_level;
                        @endphp
                        <tr>
                            <td style="font-weight: 700; color: var(--text-primary);">{{ $skill->name }}</td>
                            <td>
                                <span class="tag tag-published" style="font-weight: 600; text-transform: uppercase;">{{ $skill->category }}</span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px; width: 100%;">
                                    <div style="flex: 1; height: 6px; background-color: var(--border-color); border-radius: 4px; overflow: hidden;">
                                        <div style="width: {{ $pct }}%; height: 100%; background-color: var(--primary); border-radius: 4px;"></div>
                                    </div>
                                    <span style="font-weight: 600; color: var(--text-secondary); font-size: 12px; width: 36px; text-align: right;">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td>
                                <form method="POST" action="/admin/skills/{{ $skill->id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to delete this skill?">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-panel">
                                No skills cataloged yet. Add some above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</section>
