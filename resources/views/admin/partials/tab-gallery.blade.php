<section id="tab-gallery" class="tab">
    <div class="two-col">

        <!-- Upload/Edit Gallery Item Form -->
        <form method="POST"
              action="/admin/gallery"
              enctype="multipart/form-data"
              class="panel"
              id="gallery-form"
              data-create-action="/admin/gallery">
            @csrf
            <input type="hidden" name="_method" id="gallery-form-method" value="POST" disabled>
            <div class="panel-head">
                <div class="panel-title">
                    <i class="ti ti-photo-plus" id="gallery-form-icon"></i>
                    <span id="gallery-form-title">New Gallery Item</span>
                </div>
                <button type="button" class="btn btn-ghost btn-sm hidden" id="gallery-edit-cancel">
                    <i class="ti ti-x"></i>Cancel
                </button>
            </div>
            <div class="form-body">
                <div class="field-group">
                    <label>Title <span class="label-note">(optional)</span></label>
                    <input type="text" name="title" id="gallery-title" class="field" placeholder="e.g. Workspace setup">
                </div>
                <div class="field-group">
                    <label>Short Description <span class="label-note">(optional)</span></label>
                    <textarea name="short_description" id="gallery-description" class="field" rows="3" placeholder="e.g. Working on a weekend coding session..."></textarea>
                </div>
                <div class="field-group">
                    <label>Category <span class="required-star">*</span></label>
                    <select name="category" id="gallery-category" class="field" required>
                        <option value="Sports">Sports</option>
                        <option value="Music">Music</option>
                        <option value="Travel">Travel</option>
                        <option value="Photography">Photography</option>
                        <option value="Gaming">Gaming</option>
                        <option value="Workstation">Workstation</option>
                        <option value="Coffee">Coffee</option>
                        <option value="Learning">Learning</option>
                        <option value="Events">Events</option>
                        <option value="Lifestyle">Lifestyle</option>
                        <option value="Nature">Nature</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="field-group">
                    <label>Image File <span class="required-star" id="gallery-image-required">*</span></label>
                    <div id="gallery-dropzone" class="dropzone" style="min-height: 160px;">
                        <div class="dropzone-prompt">
                            <i class="ti ti-cloud-upload dropzone-icon"></i>
                            <span class="dropzone-text">Drag &amp; Drop image here or click to browse</span>
                        </div>
                        <input type="file" name="image" id="gallery-image-input" class="hidden-file-input" accept="image/*">
                        <div id="gallery-preview-container" class="preview-container hidden">
                            <img id="gallery-preview-img" src="" alt="Preview">
                            <button type="button" id="remove-preview-btn" class="btn-remove-preview" title="Remove image">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>
                    <p class="field-hint" id="gallery-image-hint">Max size: 5MB. Formats: JPG, PNG, WebP, GIF.</p>
                </div>

                <div class="field-group">
                    <label>Display Order</label>
                    <input type="number" name="display_order" id="gallery-display-order" class="field" value="0" min="0" required>
                </div>

                <div class="d-grid-2" style="margin-top: 10px; gap: 16px;">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" id="gallery-is-featured" value="1">
                        <span>Feature Image <span class="label-note">(shows first)</span></span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_published" id="gallery-is-published" value="1" checked>
                        <span>Publish <span class="label-note">(visible to public)</span></span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary full-width" style="margin-top: 20px;" id="gallery-submit-btn">
                    <i class="ti ti-device-floppy"></i> Save Gallery Item
                </button>
            </div>
        </form>

        <!-- Existing Items List (Draggable Sortable) -->
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">
                    <i class="ti ti-list"></i> Gallery Items <span class="label-note">(Drag cards to reorder)</span>
                </div>
            </div>
            <div class="form-body">
                <div class="gallery-admin-sortable" id="gallery-items-list">
                    @forelse($galleryItems as $item)
                        <article class="gallery-admin-card" data-id="{{ $item->id }}" draggable="true" style="background-color: var(--bg-card);">
                            <div class="card-drag-handle" title="Drag to reorder">
                                <i class="ti ti-selector"></i>
                            </div>
                            <div class="card-img-wrap">
                                <img src="{{ $item->image['thumbnail'] ?? '' }}" alt="{{ $item->title ?? 'Gallery item' }}" class="card-img">
                                <span class="card-category-badge">{{ $item->category }}</span>
                            </div>
                            <div class="card-info">
                                <div class="card-title">{{ $item->title ?? 'Untitled' }}</div>
                                @if($item->short_description)
                                    <div class="card-desc">{{ Str::limit($item->short_description, 60) }}</div>
                                @else
                                    <div class="card-desc muted" style="color: var(--text-muted);">No description provided.</div>
                                @endif
                                <div class="card-meta">
                                    <span class="meta-order">Order: <strong>{{ $item->display_order }}</strong></span>
                                    <div class="meta-badges">
                                        @if($item->is_featured)
                                            <span class="tag tag-featured" title="Featured"><i class="ti ti-star-filled"></i></span>
                                        @endif
                                        @if($item->is_published)
                                            <span class="tag tag-published" title="Published"><i class="ti ti-eye"></i></span>
                                        @else
                                            <span class="tag tag-unpublished" title="Draft"><i class="ti ti-eye-off"></i></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-actions">
                                <button type="button" class="btn btn-ghost btn-sm btn-edit-gallery" data-id="{{ $item->id }}" title="Edit Details">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button type="button" class="btn btn-ghost btn-sm btn-toggle-publish {{ $item->is_published ? 'is-active' : '' }}" data-id="{{ $item->id }}" title="{{ $item->is_published ? 'Unpublish' : 'Publish' }}">
                                    <i class="ti {{ $item->is_published ? 'ti-eye' : 'ti-eye-off' }}"></i>
                                </button>
                                <button type="button" class="btn btn-ghost btn-sm btn-toggle-featured {{ $item->is_featured ? 'is-active' : '' }}" data-id="{{ $item->id }}" title="{{ $item->is_featured ? 'Unfeature' : 'Feature' }}">
                                    <i class="ti {{ $item->is_featured ? 'ti-star-filled' : 'ti-star' }}"></i>
                                </button>
                                <form method="POST" action="/admin/gallery/{{ $item->id }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to delete this gallery item? This will remove all optimized image sizes from storage.">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Serialized JSON block for JS parsing -->
                            <script type="application/json" id="gallery-data-{{ $item->id }}">{!! json_encode([
                                'id' => $item->id,
                                'title' => $item->title,
                                'short_description' => $item->short_description,
                                'category' => $item->category,
                                'display_order' => $item->display_order,
                                'is_featured' => $item->is_featured,
                                'is_published' => $item->is_published,
                                'image_thumbnail' => $item->image['thumbnail'] ?? '',
                                'image_medium' => $item->image['medium'] ?? '',
                                'image_original' => $item->image['original'] ?? '',
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
                        </article>
                    @empty
                        <div class="empty-panel" style="grid-column: 1 / -1;">
                            No gallery items yet. Add one on the left.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</section>
