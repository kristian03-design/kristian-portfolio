<section id="tab-certifications" class="tab">
    
    <!-- Upload and Scan Form -->
    <form method="POST" action="/admin/certifications" enctype="multipart/form-data" class="ocr-form-container" style="margin-bottom: 24px;">
        @csrf
        <div class="cert-form-grid" style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 24px;">
            
            <!-- Left Side: OCR Drag & Drop Zone -->
            <div class="panel cert-upload-panel" style="margin-bottom: 0;">
                <div class="panel-head">
                    <div class="panel-title">
                        <i class="ti ti-scan"></i> Certificate Image OCR Scan
                    </div>
                </div>
                <div class="form-body">
                    <!-- Dropzone -->
                    <div class="dropzone" id="ocr-dropzone" style="min-height: 180px;">
                        <i class="ti ti-cloud-upload dropzone-icon"></i>
                        <span class="dropzone-text">Drag &amp; drop certificate image or PDF here or click to browse</span>
                        <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">PNG, JPG, JPEG, PDF (Max 5MB)</p>
                        <input type="file" name="certificate_image" id="cert-image-ocr" accept="image/*,application/pdf" class="hidden-file-input">
                    </div>

                    <!-- Image Preview -->
                    <div class="ocr-preview-container" id="ocr-preview-container" style="display: none; margin-top: 16px;">
                        <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px;">Upload Preview</div>
                        <div style="border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-color); max-height: 200px;">
                            <img id="cert-preview" src="#" alt="Certificate Preview" style="width: 100%; height: auto; object-fit: contain; max-height: 200px; display: block;">
                        </div>
                        <button type="button" class="btn btn-danger btn-sm" id="btn-remove-preview" style="margin-top: 10px;">
                            <i class="ti ti-trash"></i> Remove Image
                        </button>
                    </div>

                    <!-- Loading Progress Indicator -->
                    <div class="ocr-loading-state" id="ocr-loading-state" style="display: none; margin-top: 16px; background: var(--bg-base); border: 1px solid var(--border-color); padding: 16px; border-radius: var(--radius-sm);">
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--text-primary);">
                            <i class="ti ti-loader-2 animate-spin" style="font-size: 18px; color: var(--primary);"></i>
                            <span class="ocr-loading-text">Scanning Certificate Content...</span>
                        </div>
                        <div style="height: 6px; background-color: var(--border-color); border-radius: 4px; overflow: hidden; margin-top: 10px;">
                            <div class="ocr-progress-bar" id="ocr-progress-bar" style="width: 0%; height: 100%; background-color: var(--primary); border-radius: 4px; transition: width 0.2s ease;"></div>
                        </div>
                        <div class="ocr-progress-percent" id="ocr-progress-percent" style="font-size: 11px; color: var(--text-muted); text-align: right; margin-top: 4px;">0%</div>
                    </div>

                    <!-- Validation Alerts -->
                    <div class="ocr-validation-warning alert-feedback alert-error" id="ocr-validation-warning" style="display: none; margin-top: 16px;">
                        <i class="ti ti-alert-triangle" style="font-size: 18px;"></i>
                        <div>
                            <strong>Scan completed:</strong> Please check all fields on the right to verify details or complete missing fields manually.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Details Fields -->
            <div class="panel" style="margin-bottom: 0;">
                <div class="panel-head">
                    <div class="panel-title">
                        <i class="ti ti-edit"></i> Certificate Credentials
                    </div>
                </div>
                <div class="form-body">
                    <div class="field-group">
                        <label>Certificate Title <span class="required-star">*</span></label>
                        <input type="text" name="title" id="cert_title" class="field" placeholder="e.g. Google IT Support Professional Certificate" required>
                    </div>
                    <div class="field-group">
                        <label>Recipient Name</label>
                        <input type="text" name="recipient_name" id="cert_recipient_name" class="field" placeholder="e.g. Kristian Hernandez">
                    </div>
                    <div class="field-group">
                        <label>Course / Specialization</label>
                        <input type="text" name="course_name" id="cert_course_name" class="field" placeholder="e.g. Technical Support Fundamentals">
                    </div>
                    <div class="field-group">
                        <label>Issuing Organization <span class="required-star">*</span></label>
                        <input type="text" name="issuer" id="cert_issuer" class="field" placeholder="e.g. Coursera / Google" required>
                    </div>
                    <div class="d-grid-2">
                        <div class="field-group">
                            <label>Date Issued <span class="required-star">*</span></label>
                            <input type="date" name="issue_date" id="cert_issue_date" class="field" required>
                        </div>
                        <div class="field-group">
                            <label>Expiration Date <span class="label-note">(Optional)</span></label>
                            <input type="date" name="expiration_date" id="cert_expiration_date" class="field">
                        </div>
                    </div>
                    <div class="field-group">
                        <label>Credential ID / Serial <span class="label-note">(Optional)</span></label>
                        <input type="text" name="credential_id" id="cert_credential_id" class="field" placeholder="e.g. ABC123XYZ">
                    </div>
                    <div class="field-group">
                        <label>Verification URL <span class="label-note">(Optional)</span></label>
                        <input type="url" name="credential_url" id="cert_credential_url" class="field" placeholder="https://coursera.org/verify/...">
                    </div>
                    <div class="field-group">
                        <label>Description / Skills Acquired</label>
                        <textarea name="description" id="cert_description" class="field" rows="4" placeholder="Summary of certifications skills..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary full-width" style="margin-top: 10px;">
                        <i class="ti ti-device-floppy"></i> Save Certificate
                    </button>
                </div>
            </div>

        </div>
    </form>

    <!-- Existing Certifications Index -->
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title">
                <i class="ti ti-list"></i> Uploaded Certifications
            </div>
        </div>
        <div class="form-body">
            <div class="gallery-admin-sortable">
                @forelse($certifications as $cert)
                    @php
                        $certPath = $cert->image_path ? ltrim($cert->image_path, '/') : null;
                        $certUrl = $certPath ? asset($certPath) : null;
                        $isPdfCert = $certPath && strtolower(pathinfo($certPath, PATHINFO_EXTENSION)) === 'pdf';
                    @endphp
                    <article class="gallery-admin-card" style="background-color: var(--bg-card);">
                        <div class="card-img-wrap" style="background-color: var(--bg-base); display: flex; align-items: center; justify-content: center; height: 140px;">
                            @if($certUrl && $isPdfCert)
                                <a href="{{ $certUrl }}" target="_blank" rel="noopener" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: var(--text-primary); gap: 8px;">
                                    <i class="ti ti-file-type-pdf" style="font-size: 40px; color: var(--danger);"></i>
                                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase;">View PDF Link</span>
                                </a>
                            @elseif($certUrl)
                                <img src="{{ $certUrl }}" alt="{{ $cert->title }}" class="card-img" style="object-fit: contain; max-height: 140px;">
                            @else
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);">
                                    <i class="ti ti-photo-off" style="font-size: 32px;"></i>
                                    <span style="font-size: 11px;">No Media</span>
                                </div>
                            @endif
                        </div>
                        <div class="card-info" style="padding: 14px;">
                            <div style="font-size: 10px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ \Carbon\Carbon::parse($cert->issue_date)->format('M Y') }}
                                @if($cert->expiration_date)
                                    &mdash; {{ \Carbon\Carbon::parse($cert->expiration_date)->format('M Y') }}
                                @else
                                    &mdash; Permanent
                                @endif
                            </div>
                            <div class="card-title" style="font-size: 13.5px; font-weight: 700; margin-top: 4px; color: var(--text-primary);">{{ $cert->title }}</div>
                            <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 6px;">
                                <div><strong>Issuer:</strong> {{ $cert->issuer }}</div>
                                @if($cert->recipient_name)
                                    <div><strong>Recipient:</strong> {{ $cert->recipient_name }}</div>
                                @endif
                                @if($cert->course_name)
                                    <div><strong>Course:</strong> {{ $cert->course_name }}</div>
                                @endif
                                @if($cert->credential_id)
                                    <div style="font-family: var(--font-mono); font-size: 10px; margin-top: 2px;"><strong>ID:</strong> {{ $cert->credential_id }}</div>
                                @endif
                            </div>
                            @if($cert->description)
                                <p style="font-size: 12px; color: var(--text-muted); margin-top: 8px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">{{ $cert->description }}</p>
                            @endif
                        </div>
                        <div class="card-actions" style="padding: 10px 14px; display: flex; justify-content: flex-end; gap: 6px; background-color: rgba(0,0,0,0.01);">
                            @if($cert->credential_url)
                                <a href="{{ $cert->credential_url }}" target="_blank" class="btn btn-ghost btn-sm" title="Verify Online Credentials">
                                    <i class="ti ti-external-link"></i> Verify
                                </a>
                            @endif
                            <form method="POST" action="/admin/certifications/{{ $cert->id }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to delete this certification?">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="empty-panel" style="grid-column: 1 / -1;">
                        No certifications uploaded yet. Use the OCR form above to upload and parse.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</section>
