<!-- PROJECT DETAILS DRAWER — slide-in overlay for editing all rich case-study fields -->
<div id="details-overlay" class="details-overlay hidden" onclick="closeDetailsDrawer(event)"></div>

<aside id="details-drawer" class="details-drawer hidden" aria-label="Edit Project Details">

    <!-- Drawer header -->
    <div class="details-drawer-header">
        <div class="details-drawer-title" style="display: flex; align-items: center; gap: 8px;">
            <i class="ti ti-layout-list"></i>
            <span id="details-drawer-project-name">Project Details</span>
            <span id="details-doc-status-badge" class="doc-status-badge under-development">
                <span class="status-dot"></span>
                <span class="status-text">Under Development</span>
            </span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a id="details-view-link" href="#" target="_blank" class="btn btn-ghost btn-sm">
                <i class="ti ti-external-link"></i>View Page
            </a>
            <button type="button" class="btn btn-ghost btn-sm" onclick="closeDetailsDrawer()">
                <i class="ti ti-x"></i>
            </button>
        </div>
    </div>

    <!-- Inner tab nav -->
    <nav class="details-tab-nav">
        <button type="button" class="dtab active" data-dtab="meta">Meta</button>
        <button type="button" class="dtab" data-dtab="metrics">Metrics</button>
        <button type="button" class="dtab" data-dtab="overview">Overview</button>
        <button type="button" class="dtab" data-dtab="features">Features</button>
        <button type="button" class="dtab" data-dtab="architecture">Architecture</button>
        <button type="button" class="dtab" data-dtab="challenges">Challenges</button>
        <button type="button" class="dtab" data-dtab="timeline">Timeline</button>
        <button type="button" class="dtab" data-dtab="performance">Performance</button>
        <button type="button" class="dtab" data-dtab="security">Security</button>
        <button type="button" class="dtab" data-dtab="gallery">Gallery</button>
    </nav>

    <!-- THE FORM — wraps all tab content -->
    <form id="details-form" method="POST" action="" class="details-form-body">
        @csrf
        @method('PATCH')

        <!-- Hidden JSON fields serialized by JS before submit -->
        <input type="hidden" name="metrics"          id="d-json-metrics">
        <input type="hidden" name="overview"         id="d-json-overview">
        <input type="hidden" name="gallery"          id="d-json-gallery">
        <input type="hidden" name="features"         id="d-json-features">
        <input type="hidden" name="architecture"     id="d-json-architecture">
        <input type="hidden" name="challenges"       id="d-json-challenges">
        <input type="hidden" name="timeline"         id="d-json-timeline">
        <input type="hidden" name="performance"      id="d-json-performance">
        <input type="hidden" name="security_details" id="d-json-security_details">

        <!-- ── TAB: META ── -->
        <div class="dtab-panel active" id="dpanel-meta">
            <div class="details-section-label">Basic Info &amp; Links</div>
            <div class="d-grid-2">
                <div class="field-group">
                    <label>URL Slug <span class="label-note">(e.g. btech-admissions)</span></label>
                    <input type="text" name="slug" id="d-slug" class="field" placeholder="my-project-slug">
                </div>
                <div class="field-group">
                    <label>Category <span class="label-note">(e.g. Web Application)</span></label>
                    <input type="text" name="category" id="d-category" class="field" placeholder="Web Application">
                </div>
                <div class="field-group">
                    <label>Status</label>
                    <select name="status" id="d-status" class="field">
                        <option value="Published">Published</option>
                        <option value="Draft">Draft</option>
                        <option value="Completed">Completed</option>
                        <option value="In Progress">In Progress</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Archived">Archived</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Documentation Status</label>
                    <select name="documentation_status" id="d-documentation_status" class="field">
                        <option value="under_development">Under Development</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Duration <span class="label-note">(e.g. 3 Months)</span></label>
                    <input type="text" name="duration" id="d-duration" class="field" placeholder="3 Months">
                </div>
                <div class="field-group">
                    <label>Your Role <span class="label-note">(e.g. Solo Developer)</span></label>
                    <input type="text" name="role" id="d-role" class="field" placeholder="Solo Developer">
                </div>
                <div class="field-group">
                    <label>Documentation URL</label>
                    <input type="url" name="documentation_url" id="d-documentation_url" class="field" placeholder="https://...">
                </div>
                <div class="field-group" style="grid-column: 1 / -1;">
                    <label>Video Demo URL <span class="label-note">(YouTube embed URL)</span></label>
                    <input type="text" name="video_demo_url" id="d-video_demo_url" class="field" placeholder="https://www.youtube.com/embed/...">
                </div>
            </div>
        </div>

        <!-- ── TAB: METRICS ── -->
        <div class="dtab-panel" id="dpanel-metrics">
            <div class="details-section-label">Project Metrics</div>
            <div class="d-grid-2">
                <div class="field-group">
                    <label>Lines of Code <span class="label-note">(metrics.loc)</span></label>
                    <input type="text" class="field" id="dm-loc" placeholder="e.g. 12,500+">
                </div>
                <div class="field-group">
                    <label>Database Tables <span class="label-note">(metrics.db_tables)</span></label>
                    <input type="text" class="field" id="dm-db_tables" placeholder="e.g. 18">
                </div>
                <div class="field-group">
                    <label>API Endpoints <span class="label-note">(metrics.api_endpoints)</span></label>
                    <input type="text" class="field" id="dm-api_endpoints" placeholder="e.g. 24">
                </div>
                <div class="field-group">
                    <label>Core Modules <span class="label-note">(metrics.modules)</span></label>
                    <input type="text" class="field" id="dm-modules" placeholder="e.g. 7">
                </div>
                <div class="field-group">
                    <label>Completion Date <span class="label-note">(metrics.completion_date)</span></label>
                    <input type="text" class="field" id="dm-completion_date" placeholder="e.g. June 2025">
                </div>
                <div class="field-group">
                    <label>Development Time <span class="label-note">(metrics.development_time)</span></label>
                    <input type="text" class="field" id="dm-development_time" placeholder="e.g. 320 hrs">
                </div>
            </div>
        </div>

        <!-- ── TAB: OVERVIEW ── -->
        <div class="dtab-panel" id="dpanel-overview">
            <div class="details-section-label">Project Overview</div>
            <div class="field-group">
                <label>What is it? <span class="label-note">(overview.what)</span></label>
                <textarea class="field" id="dov-what" rows="3" placeholder="Describe the project..."></textarea>
            </div>
            <div class="field-group">
                <label>Why was it built? <span class="label-note">(overview.why)</span></label>
                <textarea class="field" id="dov-why" rows="3" placeholder="Reason for building..."></textarea>
            </div>
            <div class="d-grid-2">
                <div class="field-group">
                    <label>Target Users <span class="label-note">(overview.target_users)</span></label>
                    <input type="text" class="field" id="dov-target_users" placeholder="e.g. Students &amp; Admins">
                </div>
                <div class="field-group">
                    <label>Business Purpose <span class="label-note">(overview.business_purpose)</span></label>
                    <input type="text" class="field" id="dov-business_purpose" placeholder="e.g. Streamline enrollment">
                </div>
                <div class="field-group" style="grid-column: 1 / -1;">
                    <label>Expected Outcome <span class="label-note">(overview.expected_outcome)</span></label>
                    <input type="text" class="field" id="dov-expected_outcome" placeholder="e.g. Reduced processing time by 70%">
                </div>
            </div>
        </div>

        <!-- ── TAB: FEATURES ── -->
        <div class="dtab-panel" id="dpanel-features">
            <div class="details-section-label" style="display: flex; justify-content: space-between;">
                Key Features
                <button type="button" class="btn btn-ghost btn-sm" id="add-feature-btn">
                    <i class="ti ti-plus"></i>Add Feature
                </button>
            </div>
            <div id="features-list" class="dynamic-list"></div>
            <p class="field-hint" style="margin-top: 10px;">Each feature card shows a title and description on the public page.</p>
        </div>

        <!-- ── TAB: ARCHITECTURE ── -->
        <div class="dtab-panel" id="dpanel-architecture">
            <div class="details-section-label">System Architecture</div>
            <div class="field-group">
                <label>System Overview <span class="label-note">(architecture.system_architecture)</span></label>
                <textarea class="field" id="da-system_architecture" rows="4" placeholder="Describe the overall system architecture..."></textarea>
            </div>
            <div class="field-group">
                <label>Authentication Flow <span class="label-note">(architecture.auth_flow)</span></label>
                <textarea class="field field-mono" id="da-auth_flow" rows="4" placeholder="e.g. User → Login Form → Laravel Auth → Session → Dashboard"></textarea>
            </div>
            <div class="field-group">
                <label>API Communication Flow <span class="label-note">(architecture.api_flow)</span></label>
                <textarea class="field field-mono" id="da-api_flow" rows="4" placeholder="e.g. Client → Axios → Laravel Route → Controller → JSON"></textarea>
            </div>
            <div class="field-group">
                <label>Database ERD Schema <span class="label-note">(architecture.database_erd)</span></label>
                <textarea class="field field-mono" id="da-database_erd" rows="6" placeholder="Table schemas in text form..."></textarea>
            </div>
            <div class="field-group">
                <label>Directory Structure <span class="label-note">(architecture.folder_structure)</span></label>
                <textarea class="field field-mono" id="da-folder_structure" rows="6" placeholder="/app\n  /Models\n  /Controllers\n/resources..."></textarea>
            </div>
        </div>

        <!-- ── TAB: CHALLENGES ── -->
        <div class="dtab-panel" id="dpanel-challenges">
            <div class="details-section-label">Challenges &amp; Resolutions</div>
            <div class="field-group">
                <label>⚠️ The Problem <span class="label-note">(challenges.problem)</span></label>
                <textarea class="field" id="dc-problem" rows="4" placeholder="What was the core challenge?"></textarea>
            </div>
            <div class="field-group">
                <label>💡 The Solution <span class="label-note">(challenges.solution)</span></label>
                <textarea class="field" id="dc-solution" rows="4" placeholder="How was it solved?"></textarea>
            </div>
            <div class="field-group">
                <label>✅ The Result <span class="label-note">(challenges.result)</span></label>
                <textarea class="field" id="dc-result" rows="4" placeholder="What was the outcome?"></textarea>
            </div>
        </div>

        <!-- ── TAB: TIMELINE ── -->
        <div class="dtab-panel" id="dpanel-timeline">
            <div class="details-section-label" style="display: flex; justify-content: space-between;">
                Development Timeline
                <button type="button" class="btn btn-ghost btn-sm" id="add-timeline-btn">
                    <i class="ti ti-plus"></i>Add Phase
                </button>
            </div>
            <div id="timeline-list" class="dynamic-list"></div>
            <p class="field-hint" style="margin-top: 10px;">Each phase has a name (key) and description. Displayed as numbered steps.</p>
        </div>

        <!-- ── TAB: PERFORMANCE ── -->
        <div class="dtab-panel" id="dpanel-performance">
            <div class="details-section-label">Performance &amp; Optimization</div>
            <div class="d-grid-2">
                <div class="field-group">
                    <label>Performance Score <span class="label-note">(0-100)</span></label>
                    <input type="number" class="field" id="dp-performance_score" min="0" max="100" placeholder="90">
                </div>
                <div class="field-group">
                    <label>Accessibility Score <span class="label-note">(0-100)</span></label>
                    <input type="number" class="field" id="dp-accessibility" min="0" max="100" placeholder="90">
                </div>
            </div>
            <div class="field-group">
                <label>Lazy Loading <span class="label-note">(performance.lazy_loading)</span></label>
                <input type="text" class="field" id="dp-lazy_loading" placeholder="e.g. Implemented on all images">
            </div>
            <div class="field-group">
                <label>Caching Strategy <span class="label-note">(performance.caching)</span></label>
                <input type="text" class="field" id="dp-caching" placeholder="e.g. Laravel cache + database query caching">
            </div>
            <div class="field-group">
                <label>Image Optimization <span class="label-note">(performance.image_optimization)</span></label>
                <input type="text" class="field" id="dp-image_optimization" placeholder="e.g. Compressed and served as WebP">
            </div>
            <div class="field-group">
                <label>SEO Optimization <span class="label-note">(performance.seo)</span></label>
                <input type="text" class="field" id="dp-caching" placeholder="e.g. Meta tags, semantic HTML, sitemap">
            </div>
        </div>

        <!-- ── TAB: SECURITY ── -->
        <div class="dtab-panel" id="dpanel-security">
            <div class="details-section-label" style="display: flex; justify-content: space-between;">
                Security Implementations
                <button type="button" class="btn btn-ghost btn-sm" id="add-security-btn">
                    <i class="ti ti-plus"></i>Add Entry
                </button>
            </div>
            <div id="security-list" class="dynamic-list"></div>
            <p class="field-hint" style="margin-top: 10px;">Each entry has a guard label (key) and a description. Shown as chips on the public page.</p>
        </div>

        <!-- ── TAB: GALLERY ── -->
        <div class="dtab-panel" id="dpanel-gallery">
            <div class="details-section-label">Interface Gallery URLs</div>
            <p class="field-hint" style="margin-bottom:16px;">Enter image URLs for each device view. These appear in the carousel on the project detail page. Use Supabase or any public URL.</p>
            <div class="field-group">
                <label>Desktop View URL <span class="label-note">(gallery.desktop)</span></label>
                <input type="text" class="field" id="dg-desktop" placeholder="https://... or /images/projects/...">
            </div>
            <div class="field-group">
                <label>Tablet View URL <span class="label-note">(gallery.tablet)</span></label>
                <input type="text" class="field" id="dg-tablet" placeholder="https://... or /images/projects/...">
            </div>
            <div class="field-group">
                <label>Mobile View URL <span class="label-note">(gallery.mobile)</span></label>
                <input type="text" class="field" id="dg-mobile" placeholder="https://... or /images/projects/...">
            </div>
        </div>
    </form>

    <!-- Footer save button — outside form so it's never clipped -->
    <div class="details-drawer-footer">
        <a id="details-footer-view-link" href="#" target="_blank" class="btn btn-ghost btn-sm">
            <i class="ti ti-external-link"></i>View Page
        </a>
        <button type="submit" form="details-form" class="btn btn-primary" id="details-save-btn">
            <i class="ti ti-device-floppy"></i>Save Details
        </button>
    </div>
</aside>
