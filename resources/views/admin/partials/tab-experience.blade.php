<section id="tab-experience" class="tab">
    <div class="two-col">

        <!-- Add Experience Form -->
        <form method="POST" action="/admin/experiences" class="panel">
            @csrf
            <div class="panel-head">
                <div class="panel-title">
                    <i class="ti ti-calendar-plus"></i> Add Professional Milestone
                </div>
            </div>
            <div class="form-body">
                <div class="field-group">
                    <label>Role / Position</label>
                    <input type="text" name="role" class="field" placeholder="e.g. Senior Software Architect" required>
                </div>
                <div class="field-group">
                    <label>Company / Organization</label>
                    <input type="text" name="company" class="field" placeholder="e.g. Vercel Inc." required>
                </div>
                <div class="d-grid-2">
                    <div class="field-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="field" required>
                    </div>
                    <div class="field-group">
                        <label>End Date <span class="label-note">(Leave blank if current)</span></label>
                        <input type="date" name="end_date" class="field">
                    </div>
                </div>
                <div class="field-group">
                    <label>Key Responsibilities &amp; Achievements</label>
                    <textarea name="description" class="field" rows="5" placeholder="Highlight primary technologies, scaling objectives, and team accomplishments..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary full-width">
                    <i class="ti ti-device-floppy"></i> Save Experience
                </button>
            </div>
        </form>

        <!-- Experience Timeline List -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
            @forelse($experiences as $exp)
                <article class="panel" style="margin-bottom: 0; padding: 20px; position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }}
                                &mdash;
                                {{ $exp->is_current || !$exp->end_date ? 'PRESENT' : strtoupper(\Carbon\Carbon::parse($exp->end_date)->format('M Y')) }}
                            </div>
                            <h3 style="font-family: var(--font-display); font-size: 15px; font-weight: 700; color: var(--text-primary); margin-top: 4px;">{{ $exp->role }}</h3>
                            <div style="font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-top: 2px;">{{ $exp->company }}</div>
                        </div>
                        <form method="POST" action="/admin/experiences/{{ $exp->id }}" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to delete this experience entry?">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                    @if($exp->description)
                        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 12px; line-height: 1.5; white-space: pre-wrap;">{{ $exp->description }}</p>
                    @endif
                </article>
            @empty
                <div class="panel empty-panel">
                    No experience milestones logged.
                </div>
            @endforelse
        </div>

    </div>
</section>
