// Global Form Submit Loading & Double-Click Protection
document.addEventListener('submit', (e) => {
    const form = e.target;
    
    // Ignore if already submitting
    if (form.classList.contains('is-submitting')) {
        e.preventDefault();
        return;
    }
    
    // Locate the submit button (handling buttons inside and outside the form)
    let submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
    if (!submitBtn && form.id) {
        submitBtn = document.querySelector(`button[type="submit"][form="${form.id}"], input[type="submit"][form="${form.id}"]`);
    }
    
    if (!submitBtn) return;
    
    // Mark as submitting
    form.classList.add('is-submitting');
    submitBtn.disabled = true;
    
    // Disable cancel/reset/ghost buttons inside the form
    form.querySelectorAll('button[type="reset"], .btn-ghost, .js-cancel-btn').forEach(btn => {
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.5';
    });
    
    // Also handle cancels/resets outside the form (like project-edit-cancel or gallery-edit-cancel)
    const formName = form.id ? form.id.split('-')[0] : '';
    if (formName) {
        document.querySelectorAll(`#${formName}-edit-cancel, #${formName}-cancel-btn`).forEach(btn => {
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.5';
        });
    }

    // Determine loading text dynamically
    let loadingText = 'Processing...';
    if (submitBtn.dataset.loadingText) {
        loadingText = submitBtn.dataset.loadingText;
    } else {
        const btnText = submitBtn.textContent.trim().toLowerCase();
        if (btnText.includes('save') || btnText.includes('create') || btnText.includes('add')) {
            loadingText = 'Saving...';
        } else if (btnText.includes('update') || btnText.includes('edit')) {
            loadingText = 'Updating...';
        } else if (btnText.includes('send') || btnText.includes('reply')) {
            loadingText = 'Sending...';
        } else if (btnText.includes('delete') || btnText.includes('remove')) {
            loadingText = 'Deleting...';
        }
    }
    
    // Set loading html
    submitBtn.innerHTML = `<i class="ti ti-loader-2 animate-spin" style="margin-right: 6px;"></i> ${loadingText}`;
});

const tabTitles = {
    dashboard: 'Dashboard',
    projects: 'Projects',
    skills: 'Skills',
    experience: 'Experience',
    certifications: 'Certifications',
    messages: 'Messages',
    profile: 'Profile',
    gallery: 'Beyond Code Gallery',
};

function switchTab(name, smooth = true) {
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('[data-tab]').forEach(button => button.classList.remove('active'));

    document.getElementById(`tab-${name}`)?.classList.add('active');
    document.querySelectorAll(`[data-tab="${name}"]`).forEach(button => button.classList.add('active'));
    document.getElementById('page-title').textContent = tabTitles[name] ?? 'Dashboard';

    localStorage.setItem('admin_active_tab', name);

    if (smooth) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

document.querySelectorAll('[data-tab]').forEach(button => {
    button.addEventListener('click', () => switchTab(button.dataset.tab, true));
});

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    const activeTab = tabParam || localStorage.getItem('admin_active_tab');
    if (activeTab && tabTitles[activeTab]) {
        switchTab(activeTab, false);
    }
});

document.querySelectorAll('[data-confirm]').forEach(button => {
    button.addEventListener('click', event => {
        if (!window.confirm(button.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

document.querySelectorAll('.js-skill-fill').forEach(fill => {
    fill.style.width = `${fill.dataset.level}%`;
    fill.style.background = fill.dataset.color;
});

document.querySelectorAll('.js-skill-pct').forEach(label => {
    label.style.color = label.dataset.color;
});

// --------- PROJECT EDITING ---------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('project-form');
    if (!form) return;

    const methodInput = document.getElementById('project-form-method');
    const titleEl = document.getElementById('project-form-title');
    const iconEl = document.getElementById('project-form-icon');
    const cancelBtn = document.getElementById('project-edit-cancel');
    const submitText = document.querySelector('#project-submit span');
    const imageHint = document.getElementById('project-image-hint');
    const createAction = form.dataset.createAction;

    const fields = {
        title: form.querySelector('[name="title"]'),
        description: form.querySelector('[name="description"]'),
        url: form.querySelector('[name="url"]'),
        githubUrl: form.querySelector('[name="github_url"]'),
        customTech: form.querySelector('[name="custom_tech_stack"]'),
        image: form.querySelector('[name="image"]'),
    };

    const checkboxes = Array.from(form.querySelectorAll('.tech-checkbox'));

    function resetProjectForm() {
        form.action = createAction;
        form.reset();
        methodInput.disabled = true;
        methodInput.value = 'POST';
        titleEl.textContent = 'New Project';
        iconEl.className = 'ti ti-folder-plus';
        submitText.textContent = 'Save Project';
        cancelBtn.classList.add('hidden');
        imageHint.classList.add('hidden');
        document.querySelectorAll('.project-card.editing').forEach(card => card.classList.remove('editing'));
    }

    function readProjectData(id) {
        const dataEl = document.getElementById(`project-data-${id}`);
        if (!dataEl) return null;

        try {
            return JSON.parse(dataEl.textContent);
        } catch (error) {
            console.error('Could not read project data.', error);
            return null;
        }
    }

    function fillProjectForm(project) {
        form.action = `/admin/projects/${project.id}`;
        methodInput.disabled = false;
        methodInput.value = 'PATCH';
        titleEl.textContent = 'Edit Project';
        iconEl.className = 'ti ti-edit';
        submitText.textContent = 'Update Project';
        cancelBtn.classList.remove('hidden');
        imageHint.classList.toggle('hidden', !project.image_path);

        fields.title.value = project.title || '';
        fields.description.value = project.description || '';
        fields.url.value = project.url || '';
        fields.githubUrl.value = project.github_url || '';
        fields.customTech.value = '';
        fields.image.value = '';

        const selectedTech = new Set((project.tech_stack || []).map(tech => tech.trim()).filter(Boolean));
        const knownTech = new Set(checkboxes.map(checkbox => checkbox.value));
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectedTech.has(checkbox.value);
        });

        fields.customTech.value = Array.from(selectedTech)
            .filter(tech => !knownTech.has(tech))
            .join(', ');

        document.querySelectorAll('.project-card.editing').forEach(card => card.classList.remove('editing'));
        document.getElementById(`project-card-${project.id}`)?.classList.add('editing');
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        fields.title.focus({ preventScroll: true });
    }

    document.querySelectorAll('.project-edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            const project = readProjectData(button.dataset.projectId);
            if (project) {
                fillProjectForm(project);
            }
        });
    });

    cancelBtn.addEventListener('click', resetProjectForm);
});

// --------- TOAST SYSTEM -------------------------------------------------------------
window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.style.background = type === 'success' ? '#5f6f1d' : '#b83a3a';
    toast.style.color = '#ffffff';
    toast.style.padding = '12px 20px';
    toast.style.borderRadius = '8px';
    toast.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
    toast.style.fontSize = '13px';
    toast.style.fontWeight = '500';
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.gap = '8px';
    toast.style.minWidth = '250px';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(20px)';
    toast.style.transition = 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';

    const icon = document.createElement('i');
    icon.className = type === 'success' ? 'ti ti-circle-check' : 'ti ti-alert-circle';
    icon.style.fontSize = '16px';

    const text = document.createElement('span');
    text.textContent = message;

    toast.appendChild(icon);
    toast.appendChild(text);
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    }, 10);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 4000);
};

// --------- OCR CERTIFICATE AUTO-FILL -----------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    const dropzone = document.getElementById('ocr-dropzone');
    const fileInput = document.getElementById('cert-image-ocr');
    const previewContainer = document.getElementById('ocr-preview-container');
    const previewImg = document.getElementById('cert-preview');
    const removeBtn = document.getElementById('btn-remove-preview');
    const loadingState = document.getElementById('ocr-loading-state');
    const progressBar = document.getElementById('ocr-progress-bar');
    const progressPercent = document.getElementById('ocr-progress-percent');
    const warningAlert = document.getElementById('ocr-validation-warning');

    // Form inputs
    const fTitle = document.getElementById('cert_title');
    const fRecipient = document.getElementById('cert_recipient_name');
    const fCourse = document.getElementById('cert_course_name');
    const fIssuer = document.getElementById('cert_issuer');
    const fDate = document.getElementById('cert_issue_date');
    const fId = document.getElementById('cert_credential_id');
    const fUrl = document.getElementById('cert_credential_url');
    const fDesc = document.getElementById('cert_description');

    if (!fileInput) return;

    const ocrScripts = [
        'https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js',
        'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js',
    ];
    const loadedScripts = new Map();

    function loadScriptOnce(src) {
        if (loadedScripts.has(src)) {
            return loadedScripts.get(src);
        }

        const existing = document.querySelector(`script[src="${src}"]`);
        if (existing) {
            const promise = Promise.resolve();
            loadedScripts.set(src, promise);
            return promise;
        }

        const promise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = resolve;
            script.onerror = () => reject(new Error(`Could not load ${src}`));
            document.head.appendChild(script);
        });

        loadedScripts.set(src, promise);
        return promise;
    }

    async function ensureOcrLibraries() {
        await Promise.all(ocrScripts.map(loadScriptOnce));

        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
        }
    }

    // Drag-and-drop triggers
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.classList.add('drag-hover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.classList.remove('drag-hover');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length) {
            fileInput.files = files;
            handleFile(files[0]);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            handleFile(e.target.files[0]);
        }
    });

    removeBtn.addEventListener('click', () => {
        fileInput.value = '';
        previewImg.src = '#';
        previewContainer.style.display = 'none';
        dropzone.style.display = 'block';
        warningAlert.style.display = 'none';
        
        // Reset fields
        fTitle.value = '';
        fRecipient.value = '';
        fCourse.value = '';
        fIssuer.value = '';
        fDate.value = '';
        fId.value = '';
        fUrl.value = '';
        fDesc.value = '';
    });

    async function handleFile(file) {
        warningAlert.style.display = 'none';
        loadingState.style.display = 'flex';
        progressBar.style.width = '0%';
        progressPercent.textContent = '0%';

        try {
            await ensureOcrLibraries();
        } catch (error) {
            console.error('OCR library loading error:', error);
            loadingState.style.display = 'none';
            window.showToast('Certificate scanner could not load. Please check your internet connection.', 'error');
            return;
        }
        
        if (file.type === 'application/pdf' || file.name.endsWith('.pdf')) {
            handlePDF(file);
        } else if (file.type.match('image.*')) {
            handleImage(file);
        } else {
            loadingState.style.display = 'none';
            window.showToast('Please upload an image or PDF file.', 'error');
        }
    }

    function handleImage(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
            dropzone.style.display = 'none';
            
            runOCR(e.target.result);
        };
        reader.readAsDataURL(file);
    }

    function handlePDF(file) {
        const reader = new FileReader();
        reader.onload = async (e) => {
            const typedarray = new Uint8Array(e.target.result);
            
            loadingState.style.display = 'flex';
            progressBar.style.width = '0%';
            progressPercent.textContent = '0%';
            
            try {
                if (typeof pdfjsLib === 'undefined') {
                    throw new Error('PDF.js library is not loaded. Check internet connection or CDN.');
                }
                
                const pdf = await pdfjsLib.getDocument({ data: typedarray }).promise;
                if (pdf.numPages === 0) {
                    throw new Error('The PDF has no pages.');
                }
                
                const page = await pdf.getPage(1);
                
                // Render page to canvas for preview and scanned OCR
                const viewport = page.getViewport({ scale: 1.5 });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                await page.render({ canvasContext: context, viewport: viewport }).promise;
                
                // Show canvas data URL as preview image
                const canvasDataUrl = canvas.toDataURL('image/png');
                previewImg.src = canvasDataUrl;
                previewContainer.style.display = 'block';
                dropzone.style.display = 'none';
                
                // 1. Try digital text extraction
                const textContent = await page.getTextContent();
                const extractedText = textContent.items.map(item => item.str).join(' ');
                
                if (extractedText.trim().length < 30) {
                    console.log('PDF digital text is empty or too short. Running OCR on rendered page...');
                    runOCR(canvasDataUrl);
                } else {
                    console.log('PDF digital text extracted successfully.');
                    parseAndFill(extractedText);
                }
            } catch (err) {
                console.error('PDF parsing error:', err);
                window.showToast('Failed to parse PDF: ' + err.message, 'error');
                loadingState.style.display = 'none';
            }
        };
        reader.readAsArrayBuffer(file);
    }

    async function runOCR(imageSrc) {
        loadingState.style.display = 'flex';
        warningAlert.style.display = 'none';
        progressBar.style.width = '0%';
        progressPercent.textContent = '0%';

        try {
            if (typeof Tesseract === 'undefined') {
                throw new Error('Tesseract library is not loaded. Check internet connection or CDN availability.');
            }

            const worker = await Tesseract.createWorker('eng', 1, {
                logger: (m) => {
                    if (m.status === 'recognizing text') {
                        const pct = Math.round(m.progress * 100);
                        progressBar.style.width = `${pct}%`;
                        progressPercent.textContent = `${pct}%`;
                    }
                }
            });

            const { data: { text } } = await worker.recognize(imageSrc);
            await worker.terminate();

            parseAndFill(text);
        } catch (err) {
            console.error('OCR Error:', err);
            window.showToast('Failed to scan certificate image: ' + err.message, 'error');
            loadingState.style.display = 'none';
        }
    }

    function parseAndFill(rawText) {
        loadingState.style.display = 'none';
        const lines = rawText.split('\n').map(l => l.trim()).filter(l => l.length > 0);
        
        if (lines.length === 0) {
            warningAlert.style.display = 'flex';
            window.showToast('No text could be extracted from this image.', 'error');
            return;
        }

        // --- 1. ISSUER EXTRACTION ---
        const commonIssuers = [
            { key: 'google', name: 'Google' },
            { key: 'coursera', name: 'Coursera' },
            { key: 'udemy', name: 'Udemy' },
            { key: 'microsoft', name: 'Microsoft' },
            { key: 'aws', name: 'AWS (Amazon Web Services)' },
            { key: 'amazon', name: 'Amazon' },
            { key: 'cisco', name: 'Cisco' },
            { key: 'ibm', name: 'IBM' },
            { key: 'oracle', name: 'Oracle' },
            { key: 'freecodecamp', name: 'freeCodeCamp' },
            { key: 'edx', name: 'edX' },
            { key: 'pluralsight', name: 'Pluralsight' },
            { key: 'linkedin', name: 'LinkedIn' },
            { key: 'meta', name: 'Meta' },
            { key: 'scrum.org', name: 'Scrum.org' },
            { key: 'salesforce', name: 'Salesforce' },
            { key: 'scrum alliance', name: 'Scrum Alliance' },
            { key: 'pmi', name: 'Project Management Institute (PMI)' },
            { key: 'project management institute', name: 'Project Management Institute (PMI)' }
        ];

        let foundIssuers = [];
        const lowerText = rawText.toLowerCase();
        for (const issuer of commonIssuers) {
            if (lowerText.includes(issuer.key)) {
                if (!foundIssuers.some(fi => fi.name === issuer.name)) {
                    foundIssuers.push(issuer);
                }
            }
        }

        let issuer = "";
        if (foundIssuers.length > 0) {
            issuer = foundIssuers.map(fi => fi.name).join(' / ');
        } else {
            const offerMatch = rawText.match(/(?:offered|issued|provided)\s+by\s+([A-Z][a-zA-Z\s]+)(?:\s+through|\.|\n|$)/i);
            if (offerMatch && offerMatch[1]) {
                issuer = offerMatch[1].trim();
            }
        }

        // --- 2. CREDENTIAL ID & URL EXTRACTION ---
        let credentialId = "";
        let credentialUrl = "";
        const urlMatch = rawText.match(/https?:\/\/[^\s]+/gi);
        
        if (urlMatch) {
            for (const url of urlMatch) {
                const cleanUrl = url.replace(/[\.\,\)\(\"\'\>]+$/, '');
                if (cleanUrl.includes('coursera.org/verify/')) {
                    credentialUrl = cleanUrl;
                    const parts = cleanUrl.split('/');
                    credentialId = parts[parts.length - 1] || parts[parts.length - 2];
                    break;
                } else if (cleanUrl.includes('udemy.com/certificate/')) {
                    credentialUrl = cleanUrl;
                    const parts = cleanUrl.split('/');
                    credentialId = parts[parts.length - 1] || parts[parts.length - 2];
                    break;
                } else if (cleanUrl.includes('credly.com/')) {
                    credentialUrl = cleanUrl;
                    const parts = cleanUrl.split('/');
                    credentialId = parts[parts.length - 1] || parts[parts.length - 2];
                    break;
                } else {
                    credentialUrl = cleanUrl;
                }
            }
        }

        if (!credentialId) {
            const idMatch = rawText.match(/(?:credential\s*id|certificate\s*id|cert\s*id|verify\s*code|verification\s*id|no\.|number)[:\s\-\#]+([a-zA-Z0-9\-]+)/i);
            if (idMatch && idMatch[1]) {
                credentialId = idMatch[1].trim();
            }
        }

        // --- 3. RECIPIENT NAME EXTRACTION ---
        let recipientName = "";
        for (let i = 0; i < lines.length; i++) {
            const lowerLine = lines[i].toLowerCase();
            if (
                lowerLine.includes("certify that") || 
                lowerLine.includes("awarded to") || 
                lowerLine.includes("presented to") ||
                lowerLine.includes("this certificate is") ||
                lowerLine.includes("hereby certified that")
            ) {
                for (let j = i + 1; j < Math.min(i + 4, lines.length); j++) {
                    const candidate = lines[j].trim();
                    const lowerCand = candidate.toLowerCase();
                    if (
                        candidate.length > 2 && 
                        candidate.length < 40 &&
                        !lowerCand.includes("has successfully") &&
                        !lowerCand.includes("successfully") &&
                        !lowerCand.includes("completed") &&
                        !lowerCand.includes("for completing") &&
                        !lowerCand.includes("on this") &&
                        !lowerCand.includes("certificate")
                    ) {
                        if (/^[A-Z][a-zA-Z\s\.\-\']+$/.test(candidate)) {
                            recipientName = candidate;
                            break;
                        }
                    }
                }
            }
            if (recipientName) break;
        }

        // Fallback for recipient if regex was too strict
        if (!recipientName) {
            for (let i = 0; i < lines.length; i++) {
                const lowerLine = lines[i].toLowerCase();
                if (lowerLine.includes("certify that") || lowerLine.includes("awarded to") || lowerLine.includes("presented to")) {
                    for (let j = i + 1; j < Math.min(i + 4, lines.length); j++) {
                        const candidate = lines[j].trim();
                        const lowerCand = candidate.toLowerCase();
                        if (
                            candidate.length > 2 && 
                            candidate.length < 40 && 
                            !lowerCand.includes("completed") && 
                            !lowerCand.includes("successfully") &&
                            !lowerCand.includes("certificate")
                        ) {
                            recipientName = candidate;
                            break;
                        }
                    }
                }
                if (recipientName) break;
            }
        }

        // --- 4. TITLE & PROGRAM/COURSE EXTRACTION ---
        let title = "";
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            const lowerLine = line.toLowerCase();
            
            if (i < lines.length - 1 && (
                lowerLine === "has successfully completed" ||
                lowerLine === "has successfully completed the" ||
                lowerLine === "has successfully completed the online course" ||
                lowerLine === "has successfully completed the program" ||
                lowerLine === "for successfully completing" ||
                lowerLine === "for successfully completing the" ||
                lowerLine === "for completing" ||
                lowerLine === "is hereby awarded the credential of" ||
                lowerLine === "is awarded the certificate of" ||
                lowerLine === "certificate of completion in"
            )) {
                title = lines[i + 1];
                break;
            }
            
            const inlineMatch = line.match(/(?:successfully completed|completed the online course|completion of|awarded the credential of|certificate of completion in|program certificate in)\s+["']?([^"'\.]+)/i);
            if (inlineMatch && inlineMatch[1]) {
                const candidate = inlineMatch[1].trim();
                if (candidate.length > 5 && candidate.length < 80) {
                    title = candidate;
                    break;
                }
            }
        }

        if (!title) {
            for (const line of lines) {
                const lowerLine = line.toLowerCase();
                if (
                    (lowerLine.includes('certificate') || lowerLine.includes('specialization') || lowerLine.includes('course') || lowerLine.includes('certified')) &&
                    !lowerLine.includes('has successfully') &&
                    !lowerLine.includes('this is to') &&
                    !lowerLine.includes('verify') &&
                    line.length > 5 &&
                    line.length < 80
                ) {
                    title = line;
                    break;
                }
            }
        }

        if (title) {
            title = title.replace(/^[":\-\s]+|[":\-\s\.]+$/g, '').trim();
        }

        // Program / Course Name heuristics
        let courseName = "";
        if (title) {
            // If the title contains words like "Professional Certificate" or "Specialization", the course/program name is the prefix
            if (title.toLowerCase().includes("certificate")) {
                courseName = title.replace(/\b(professional\s+)?certificate\b/gi, '').trim();
            } else if (title.toLowerCase().includes("specialization")) {
                courseName = title.replace(/\bspecialization\b/gi, '').trim();
            } else {
                courseName = title;
            }
        }

        // --- 5. ISSUE DATE EXTRACTION ---
        let issueDate = null;
        const monthNames = ["january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december"];
        const monthAbbrs = ["jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"];
        
        function getMonthNum(monthStr) {
            const idx = monthNames.indexOf(monthStr);
            if (idx !== -1) return idx + 1;
            const idxAbbr = monthAbbrs.indexOf(monthStr);
            if (idxAbbr !== -1) return idxAbbr + 1;
            return null;
        }
        
        // Try MDY format (e.g., "October 24, 2024" or "Oct 2024")
        const mdyRegex = /\b(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|jun|jul|aug|sep|oct|nov|dec)\s*(?:(\d{1,2})\s*,?)?\s*(20\d{2}|19\d{2})\b/i;
        const match1 = rawText.match(mdyRegex);
        if (match1) {
            const monthNum = getMonthNum(match1[1].toLowerCase());
            const day = match1[2] ? parseInt(match1[2]) : 1;
            const year = parseInt(match1[3]);
            if (monthNum && year) {
                issueDate = `${year}-${String(monthNum).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            }
        }
        
        // Try DMY format (e.g., "24 October 2024")
        if (!issueDate) {
            const dmyRegex = /\b(\d{1,2})\s+(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|jun|jul|aug|sep|oct|nov|dec)\s*,?\s*(20\d{2}|19\d{2})\b/i;
            const match2 = rawText.match(dmyRegex);
            if (match2) {
                const day = parseInt(match2[1]);
                const monthNum = getMonthNum(match2[2].toLowerCase());
                const year = parseInt(match2[3]);
                if (monthNum && year) {
                    issueDate = `${year}-${String(monthNum).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                }
            }
        }

        // Try YYYY-MM-DD or MM/DD/YYYY
        if (!issueDate) {
            const numRegex = /\b(\d{1,4})[/\-.](\d{1,2})[/\-.](\d{1,4})\b/;
            const match3 = rawText.match(numRegex);
            if (match3) {
                let val1 = parseInt(match3[1]);
                let val2 = parseInt(match3[2]);
                let val3 = parseInt(match3[3]);
                if (val1 > 1900 && val1 < 2100) {
                    issueDate = `${val1}-${String(val2).padStart(2, '0')}-${String(val3).padStart(2, '0')}`;
                } else if (val3 > 1900 && val3 < 2100) {
                    let month = val2;
                    let day = val1;
                    if (val1 <= 12 && val2 > 12) {
                        month = val1;
                        day = val2;
                    }
                    issueDate = `${val3}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                }
            }
        }

        // --- 6. DESCRIPTION GENERATION ---
        let description = "";
        if (title && issuer) {
            description = `Successfully completed the "${title}" offered by ${issuer}${issueDate ? ' on ' + new Date(issueDate).toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) : ''}.`;
        }

        // --- 7. AUTO-FILL ACTIONS & FLASH MICRO-ANIMATIONS ---
        const fillValue = (inputEl, val) => {
            if (inputEl && val) {
                inputEl.value = val;
                flashField(inputEl);
            }
        };

        fillValue(fTitle, title);
        fillValue(fRecipient, recipientName);
        fillValue(fCourse, courseName);
        fillValue(fIssuer, issuer);
        fillValue(fDate, issueDate);
        fillValue(fId, credentialId);
        fillValue(fUrl, credentialUrl);
        fillValue(fDesc, description);

        // --- 8. VALIDATION CHECK ---
        const missingFields = [];
        if (!title) missingFields.push('Certificate Title');
        if (!issuer) missingFields.push('Issuing Organization');
        if (!issueDate) missingFields.push('Date Issued');
        if (!recipientName) missingFields.push('Recipient Name');

        if (missingFields.length > 0) {
            warningAlert.style.display = 'flex';
            window.showToast('OCR complete. Some fields need manual validation.', 'warning');
        } else {
            warningAlert.style.display = 'none';
            window.showToast('Certificate scanned successfully! All details loaded.', 'success');
        }
    }

    function flashField(element) {
        if (!element) return;
        element.style.transition = 'none';
        element.style.backgroundColor = 'rgba(95, 111, 29, 0.18)';
        setTimeout(() => {
            element.style.transition = 'background-color 0.8s ease';
            element.style.backgroundColor = '';
        }, 100);
    }
});

// --------- REPLY MODAL CONTROLS ---------------------------------------------------
function closeReplyModal(event) {
    const modal = document.getElementById('reply-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

window.closeReplyModal = closeReplyModal;

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('reply-modal');
    if (!modal) return;

    const replyForm = document.getElementById('reply-form');
    const mName = document.getElementById('modal-sender-name');
    const mEmail = document.getElementById('modal-sender-email');
    const mDate = document.getElementById('modal-message-date');
    const mOrig = document.getElementById('modal-original-msg');
    const rSubject = document.getElementById('reply-subject');
    const rBody = document.getElementById('reply-body');

    document.querySelectorAll('.btn-reply-trigger').forEach(btn => {
        btn.addEventListener('click', () => {
            const msgId = btn.dataset.messageId;
            const dataEl = document.getElementById(`message-data-${msgId}`);
            if (!dataEl) return;

            try {
                const msg = JSON.parse(dataEl.textContent);
                mName.textContent = msg.name || 'Anonymous';
                mEmail.textContent = msg.email || '';
                mDate.textContent = msg.date || '';
                mOrig.textContent = msg.message || '';

                rSubject.value = `Re: Message from ${msg.name}`;
                rBody.value = '';

                replyForm.action = `/admin/messages/${msg.id}/reply`;
                modal.classList.remove('hidden');
            } catch (e) {
                console.error('Failed to parse message data', e);
            }
        });
    });
});

// ===================================================================================
// PROJECT DETAILS DRAWER
// ===================================================================================
(function () {
    const overlay  = document.getElementById('details-overlay');
    const drawer   = document.getElementById('details-drawer');
    const form     = document.getElementById('details-form');
    if (!drawer || !form) return;

    let currentSlug = '';

    const docStatusSelect = document.getElementById('d-documentation_status');
    const badgeEl = document.getElementById('details-doc-status-badge');
    if (docStatusSelect) {
        docStatusSelect.addEventListener('change', function () {
            updateDrawerBadge(this.value);
        });
    }

    function updateDrawerBadge(status) {
        if (!badgeEl) return;
        badgeEl.className = 'doc-status-badge ' + (status === 'published' ? 'published' : 'under-development');
        badgeEl.querySelector('.status-text').textContent = status === 'published' ? 'Published' : 'Under Development';
    }

    // ── Open / Close ──────────────────────────────────────────────────────────────
    function openDetailsDrawer(projectId) {
        const dataEl = document.getElementById(`project-data-${projectId}`);
        if (!dataEl) return;

        let p;
        try { p = JSON.parse(dataEl.textContent); }
        catch (e) { console.error('Details drawer: bad JSON', e); return; }

        // Set form action
        form.action = `/admin/projects/${p.id}/details`;
        currentSlug = p.slug || '';

        // Project name in header
        document.getElementById('details-drawer-project-name').textContent = p.title;

        // View-page links
        const viewUrl = currentSlug ? `/projects/${currentSlug}` : '#';
        document.getElementById('details-view-link').href = viewUrl;
        document.getElementById('details-footer-view-link').href = viewUrl;

        // Populate all fields
        populateMeta(p);
        populateMetrics(p.metrics || {});
        populateOverview(p.overview || {});
        populateFeatures(p.features || []);
        populateArchitecture(p.architecture || {});
        populateChallenges(p.challenges || {});
        populateTimeline(p.timeline || {});
        populatePerformance(p.performance || {});
        populateSecurity(p.security_details || {});
        populateGallery(p.gallery || {});

        // Reset to first tab
        switchDTab('meta');

        overlay.classList.remove('hidden');
        drawer.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    window.closeDetailsDrawer = function (e) {
        if (e && e.target !== overlay) return; // only close when clicking overlay itself, or direct call
        overlay.classList.add('hidden');
        drawer.classList.remove('open');
        document.body.style.overflow = '';
    };

    // Close on Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.classList.contains('open')) {
            overlay.classList.add('hidden');
            drawer.classList.remove('open');
            document.body.style.overflow = '';
        }
    });

    // Attach to all "Details" buttons
    document.querySelectorAll('.project-details-btn').forEach(btn => {
        btn.addEventListener('click', () => openDetailsDrawer(btn.dataset.projectId));
    });

    // ── Inner tab switching ────────────────────────────────────────────────────────
    function switchDTab(name) {
        document.querySelectorAll('.dtab').forEach(t => t.classList.toggle('active', t.dataset.dtab === name));
        document.querySelectorAll('.dtab-panel').forEach(p => p.classList.toggle('active', p.id === `dpanel-${name}`));
    }
    document.querySelectorAll('.dtab').forEach(t => {
        t.addEventListener('click', () => switchDTab(t.dataset.dtab));
    });

    // ── Populate helpers ──────────────────────────────────────────────────────────
    function val(id, value) {
        const el = document.getElementById(id);
        if (!el) return;
        el.value = value ?? '';
    }

    function populateMeta(p) {
        val('d-slug',              p.slug);
        val('d-category',          p.category);
        val('d-duration',          p.duration);
        val('d-role',              p.role);
        val('d-documentation_url', p.documentation_url);
        val('d-video_demo_url',    p.video_demo_url);
        const statusEl = document.getElementById('d-status');
        if (statusEl) statusEl.value = p.status || 'Completed';

        const docStatusVal = p.documentation_status || 'under_development';
        const docStatusEl = document.getElementById('d-documentation_status');
        if (docStatusEl) docStatusEl.value = docStatusVal;
        updateDrawerBadge(docStatusVal);
    }

    function populateMetrics(m) {
        val('dm-loc',              m.loc);
        val('dm-db_tables',        m.db_tables);
        val('dm-api_endpoints',    m.api_endpoints);
        val('dm-modules',          m.modules);
        val('dm-completion_date',  m.completion_date);
        val('dm-development_time', m.development_time);
    }

    function populateOverview(o) {
        val('dov-what',             o.what);
        val('dov-why',              o.why);
        val('dov-target_users',     o.target_users);
        val('dov-business_purpose', o.business_purpose);
        val('dov-expected_outcome', o.expected_outcome);
    }

    function populateArchitecture(a) {
        val('da-system_architecture', a.system_architecture);
        val('da-auth_flow',           a.auth_flow);
        val('da-api_flow',            a.api_flow);
        val('da-database_erd',        a.database_erd);
        val('da-folder_structure',    a.folder_structure);
    }

    function populateChallenges(c) {
        val('dc-problem',  c.problem);
        val('dc-solution', c.solution);
        val('dc-result',   c.result);
    }

    function populatePerformance(perf) {
        val('dp-performance_score', perf.performance_score);
        val('dp-accessibility',     perf.accessibility);
        val('dp-lazy_loading',      perf.lazy_loading);
        val('dp-caching',           perf.caching);
        val('dp-image_optimization',perf.image_optimization);
        val('dp-seo',               perf.seo);
    }

    function populateGallery(g) {
        val('dg-desktop', g.desktop);
        val('dg-tablet',  g.tablet);
        val('dg-mobile',  g.mobile);
    }

    // ── Dynamic rows ─────────────────────────────────────────────────────────────
    function makeFeaturesRow(title = '', description = '') {
        const row = document.createElement('div');
        row.className = 'dynamic-row feature-row';
        row.innerHTML = `
            <div class="field-group" style="margin:0;">
                <label style="font-size:11px;">Title</label>
                <input type="text" class="field feat-title" value="${escHtml(title)}" placeholder="Feature title">
            </div>
            <div class="field-group" style="margin:0;">
                <label style="font-size:11px;">Description</label>
                <textarea class="field feat-desc" rows="2" placeholder="Feature description...">${escHtml(description)}</textarea>
            </div>
            <button type="button" class="dynamic-row-remove" title="Remove">✕</button>`;
        row.querySelector('.dynamic-row-remove').addEventListener('click', () => row.remove());
        return row;
    }

    function makeKVRow(key = '', value = '', keyPlaceholder = 'Key', valuePlaceholder = 'Description') {
        const row = document.createElement('div');
        row.className = 'dynamic-row kv-row';
        row.innerHTML = `
            <div class="field-group" style="margin:0;">
                <label style="font-size:11px;">Label / Key</label>
                <input type="text" class="field kv-key" value="${escHtml(key)}" placeholder="${escHtml(keyPlaceholder)}">
            </div>
            <div class="field-group" style="margin:0;">
                <label style="font-size:11px;">Description</label>
                <textarea class="field kv-val" rows="2" placeholder="${escHtml(valuePlaceholder)}">${escHtml(value)}</textarea>
            </div>
            <button type="button" class="dynamic-row-remove" title="Remove">✕</button>`;
        row.querySelector('.dynamic-row-remove').addEventListener('click', () => row.remove());
        return row;
    }

    function populateFeatures(features) {
        const list = document.getElementById('features-list');
        list.innerHTML = '';
        if (Array.isArray(features)) {
            features.forEach(f => list.appendChild(makeFeaturesRow(f.title, f.description)));
        }
    }

    function populateTimeline(timeline) {
        const list = document.getElementById('timeline-list');
        list.innerHTML = '';
        if (timeline && typeof timeline === 'object') {
            Object.entries(timeline).forEach(([k, v]) => list.appendChild(makeKVRow(k, v, 'Phase name', 'Phase description')));
        }
    }

    function populateSecurity(sec) {
        const list = document.getElementById('security-list');
        list.innerHTML = '';
        if (sec && typeof sec === 'object') {
            Object.entries(sec).forEach(([k, v]) => list.appendChild(makeKVRow(k, v, 'Guard / Label', 'Security detail')));
        }
    }

    // Add-row buttons
    document.getElementById('add-feature-btn')?.addEventListener('click', () => {
        document.getElementById('features-list').appendChild(makeFeaturesRow());
    });
    document.getElementById('add-timeline-btn')?.addEventListener('click', () => {
        document.getElementById('timeline-list').appendChild(makeKVRow('', '', 'Phase name', 'Phase description'));
    });
    document.getElementById('add-security-btn')?.addEventListener('click', () => {
        document.getElementById('security-list').appendChild(makeKVRow('', '', 'Guard / Label', 'Security detail'));
    });

    // ── Pre-submit: serialize dynamic fields into hidden JSON inputs ──────────────
    form.addEventListener('submit', (e) => {
        // Metrics
        setJson('d-json-metrics', {
            loc:              document.getElementById('dm-loc')?.value.trim() || null,
            db_tables:        document.getElementById('dm-db_tables')?.value.trim() || null,
            api_endpoints:    document.getElementById('dm-api_endpoints')?.value.trim() || null,
            modules:          document.getElementById('dm-modules')?.value.trim() || null,
            completion_date:  document.getElementById('dm-completion_date')?.value.trim() || null,
            development_time: document.getElementById('dm-development_time')?.value.trim() || null,
        });

        // Overview
        setJson('d-json-overview', {
            what:             document.getElementById('dov-what')?.value.trim() || null,
            why:              document.getElementById('dov-why')?.value.trim() || null,
            target_users:     document.getElementById('dov-target_users')?.value.trim() || null,
            business_purpose: document.getElementById('dov-business_purpose')?.value.trim() || null,
            expected_outcome: document.getElementById('dov-expected_outcome')?.value.trim() || null,
        });

        // Gallery
        setJson('d-json-gallery', {
            desktop: document.getElementById('dg-desktop')?.value.trim() || null,
            tablet:  document.getElementById('dg-tablet')?.value.trim() || null,
            mobile:  document.getElementById('dg-mobile')?.value.trim() || null,
        });

        // Features (array)
        const features = [];
        document.querySelectorAll('#features-list .feature-row').forEach(row => {
            const title = row.querySelector('.feat-title')?.value.trim();
            const desc  = row.querySelector('.feat-desc')?.value.trim();
            if (title) features.push({ title, description: desc || '' });
        });
        setJson('d-json-features', features.length ? features : null);

        // Architecture
        setJson('d-json-architecture', {
            system_architecture: document.getElementById('da-system_architecture')?.value.trim() || null,
            auth_flow:           document.getElementById('da-auth_flow')?.value.trim() || null,
            api_flow:            document.getElementById('da-api_flow')?.value.trim() || null,
            database_erd:        document.getElementById('da-database_erd')?.value.trim() || null,
            folder_structure:    document.getElementById('da-folder_structure')?.value.trim() || null,
        });

        // Challenges
        setJson('d-json-challenges', {
            problem:  document.getElementById('dc-problem')?.value.trim() || null,
            solution: document.getElementById('dc-solution')?.value.trim() || null,
            result:   document.getElementById('dc-result')?.value.trim() || null,
        });

        // Timeline (object: key → description)
        const timeline = {};
        document.querySelectorAll('#timeline-list .kv-row').forEach(row => {
            const k = row.querySelector('.kv-key')?.value.trim();
            const v = row.querySelector('.kv-val')?.value.trim();
            if (k) timeline[k] = v || '';
        });
        setJson('d-json-timeline', Object.keys(timeline).length ? timeline : null);

        // Performance
        setJson('d-json-performance', {
            performance_score: document.getElementById('dp-performance_score')?.value || null,
            accessibility:     document.getElementById('dp-accessibility')?.value || null,
            lazy_loading:      document.getElementById('dp-lazy_loading')?.value.trim() || null,
            caching:           document.getElementById('dp-caching')?.value.trim() || null,
            image_optimization:document.getElementById('dp-image_optimization')?.value.trim() || null,
            seo:               document.getElementById('dp-seo')?.value.trim() || null,
        });

        // Security (object: key → description)
        const security = {};
        document.querySelectorAll('#security-list .kv-row').forEach(row => {
            const k = row.querySelector('.kv-key')?.value.trim();
            const v = row.querySelector('.kv-val')?.value.trim();
            if (k) security[k] = v || '';
        });
        setJson('d-json-security_details', Object.keys(security).length ? security : null);
    });

    function setJson(id, data) {
        const el = document.getElementById(id);
        if (el) el.value = data !== null ? JSON.stringify(data) : 'null';
    }

    function escHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    // --------- GALLERY MANAGEMENT ------------------------------------------------------
    const galleryForm = document.getElementById('gallery-form');
    if (galleryForm) {
        const methodInput = document.getElementById('gallery-form-method');
        const titleEl = document.getElementById('gallery-form-title');
        const iconEl = document.getElementById('gallery-form-icon');
        const cancelBtn = document.getElementById('gallery-edit-cancel');
        const submitBtnText = document.getElementById('gallery-submit-btn');
        const imageRequired = document.getElementById('gallery-image-required');
        const imageInput = document.getElementById('gallery-image-input');
        const dropzone = document.getElementById('gallery-dropzone');
        const previewContainer = document.getElementById('gallery-preview-container');
        const previewImg = document.getElementById('gallery-preview-img');
        const removePreviewBtn = document.getElementById('remove-preview-btn');
        const createAction = galleryForm.dataset.createAction;
            // Note: Form submission loading states and double-submit protection are handled globally at the top of this file.

        function resetGalleryForm() {
            galleryForm.action = createAction;
            galleryForm.reset();
            methodInput.disabled = true;
            methodInput.value = 'POST';
            titleEl.textContent = 'New Gallery Item';
            iconEl.className = 'ti ti-photo-plus';
            submitBtnText.innerHTML = '<i class="ti ti-device-floppy"></i> Save Gallery Item';
            cancelBtn.classList.add('hidden');
            imageRequired.classList.remove('hidden');
            imageInput.required = true;
            previewImg.src = '';
            previewContainer.classList.add('hidden');
            document.querySelectorAll('.gallery-admin-card.editing').forEach(card => card.classList.remove('editing'));
        }

        cancelBtn.addEventListener('click', resetGalleryForm);

        // Click on dropzone triggers file input
        dropzone.addEventListener('click', (e) => {
            if (e.target !== removePreviewBtn && !removePreviewBtn.contains(e.target)) {
                imageInput.click();
            }
        });

        // Drag and drop states
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            }, false);
        });

        // Handle drop event
        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length) {
                imageInput.files = files;
                handlePreview(files[0]);
            }
        });

        // Handle input change
        imageInput.addEventListener('change', () => {
            if (imageInput.files && imageInput.files[0]) {
                handlePreview(imageInput.files[0]);
            }
        });

        function handlePreview(file) {
            if (!file.type.match('image.*')) {
                if (window.showToast) window.showToast('Please upload an image file.', 'error');
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                if (window.showToast) window.showToast('Image is larger than 10MB.', 'error');
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        // Remove preview button
        removePreviewBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            imageInput.value = '';
            previewImg.src = '';
            previewContainer.classList.add('hidden');
            if (methodInput.value === 'PATCH') {
                imageRequired.classList.add('hidden');
                imageInput.required = false;
            } else {
                imageRequired.classList.remove('hidden');
                imageInput.required = true;
            }
        });

        // Edit button click handler
        document.querySelectorAll('.btn-edit-gallery').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const dataEl = document.getElementById(`gallery-data-${id}`);
                if (!dataEl) return;

                try {
                    const data = JSON.parse(dataEl.textContent);
                    
                    // Reset styling
                    document.querySelectorAll('.gallery-admin-card.editing').forEach(card => card.classList.remove('editing'));
                    const card = btn.closest('.gallery-admin-card');
                    if (card) card.classList.add('editing');

                    // Set action & method
                    galleryForm.action = `/admin/gallery/${id}`;
                    methodInput.disabled = false;
                    methodInput.value = 'PATCH';

                    // Populate fields
                    document.getElementById('gallery-title').value = data.title || '';
                    document.getElementById('gallery-description').value = data.short_description || '';
                    document.getElementById('gallery-category').value = data.category;
                    document.getElementById('gallery-display-order').value = data.display_order;
                    document.getElementById('gallery-is-featured').checked = !!data.is_featured;
                    document.getElementById('gallery-is-published').checked = !!data.is_published;

                    // Image is optional on update, show existing thumbnail in preview
                    imageInput.required = false;
                    imageRequired.classList.add('hidden');
                    if (data.image_thumbnail) {
                        previewImg.src = data.image_thumbnail;
                        previewContainer.classList.remove('hidden');
                    } else {
                        previewImg.src = '';
                        previewContainer.classList.add('hidden');
                    }

                    titleEl.textContent = 'Edit Gallery Item';
                    iconEl.className = 'ti ti-edit';
                    submitBtnText.innerHTML = '<i class="ti ti-device-floppy"></i> Update Gallery Item';
                    cancelBtn.classList.remove('hidden');

                    // Scroll to form
                    galleryForm.scrollIntoView({ behavior: 'smooth' });

                } catch (error) {
                    console.error('Error parsing gallery data', error);
                }
            });
        });

        // Toggle published click handler
        document.querySelectorAll('.btn-toggle-publish').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                fetch(`/admin/gallery/${id}/toggle-published`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || galleryForm.querySelector('[name="_token"]')?.value,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const icon = btn.querySelector('i');
                        if (data.is_published) {
                            btn.classList.add('is-active');
                            btn.title = 'Unpublish';
                            if (icon) {
                                icon.className = 'ti ti-eye';
                            }
                        } else {
                            btn.classList.remove('is-active');
                            btn.title = 'Publish';
                            if (icon) {
                                icon.className = 'ti ti-eye-off';
                            }
                        }
                        if (window.showToast) window.showToast(data.message, 'success');
                        
                        // Dynamically update card badge if any
                        const badgeContainer = btn.closest('.gallery-admin-card')?.querySelector('.meta-badges');
                        if (badgeContainer) {
                            let publishedBadge = badgeContainer.querySelector('.tag-published, .tag-unpublished');
                            if (publishedBadge) {
                                if (data.is_published) {
                                    publishedBadge.className = 'tag tag-published';
                                    publishedBadge.title = 'Published';
                                    publishedBadge.innerHTML = '<i class="ti ti-eye"></i>';
                                } else {
                                    publishedBadge.className = 'tag tag-unpublished';
                                    publishedBadge.title = 'Draft';
                                    publishedBadge.innerHTML = '<i class="ti ti-eye-off"></i>';
                                }
                            }
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    if (window.showToast) window.showToast('An error occurred.', 'error');
                });
            });
        });

        // Toggle featured click handler
        document.querySelectorAll('.btn-toggle-featured').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                fetch(`/admin/gallery/${id}/toggle-featured`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || galleryForm.querySelector('[name="_token"]')?.value,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const icon = btn.querySelector('i');
                        if (data.is_featured) {
                            btn.classList.add('is-active');
                            btn.title = 'Unfeature';
                            if (icon) {
                                icon.className = 'ti ti-star-filled';
                            }
                        } else {
                            btn.classList.remove('is-active');
                            btn.title = 'Feature';
                            if (icon) {
                                icon.className = 'ti ti-star';
                            }
                        }
                        if (window.showToast) window.showToast(data.message, 'success');

                        // Dynamically update card badge if any
                        const badgeContainer = btn.closest('.gallery-admin-card')?.querySelector('.meta-badges');
                        if (badgeContainer) {
                            let featuredBadge = badgeContainer.querySelector('.tag-featured');
                            if (data.is_featured) {
                                if (!featuredBadge) {
                                    const newBadge = document.createElement('span');
                                    newBadge.className = 'tag tag-featured';
                                    newBadge.title = 'Featured';
                                    newBadge.innerHTML = '<i class="ti ti-star-filled"></i>';
                                    badgeContainer.prepend(newBadge);
                                }
                            } else {
                                if (featuredBadge) featuredBadge.remove();
                            }
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    if (window.showToast) window.showToast('An error occurred.', 'error');
                });
            });
        });

        // HTML5 Drag and Drop sorting
        const sortableContainer = document.getElementById('gallery-items-list');
        if (sortableContainer) {
            let dragEl = null;

            sortableContainer.addEventListener('dragstart', (e) => {
                const card = e.target.closest('.gallery-admin-card');
                if (card) {
                    dragEl = card;
                    card.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                }
            });

            sortableContainer.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                const targetCard = e.target.closest('.gallery-admin-card');
                if (targetCard && targetCard !== dragEl) {
                    const bounding = targetCard.getBoundingClientRect();
                    const offset = e.clientY - bounding.top;
                    if (offset > bounding.height / 2) {
                        targetCard.after(dragEl);
                    } else {
                        targetCard.before(dragEl);
                    }
                }
            });

            sortableContainer.addEventListener('dragend', () => {
                if (dragEl) {
                    dragEl.classList.remove('dragging');
                    dragEl = null;
                    
                    // Re-calculate orders and send AJAX save request
                    const cards = Array.from(sortableContainer.querySelectorAll('.gallery-admin-card'));
                    const ids = cards.map(c => c.dataset.id);
                    
                    // Update UI order texts instantly
                    cards.forEach((c, idx) => {
                        const orderLabel = c.querySelector('.meta-order strong');
                        if (orderLabel) orderLabel.textContent = idx + 1;
                    });

                    fetch('/admin/gallery/reorder', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || galleryForm.querySelector('[name="_token"]')?.value,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && window.showToast) {
                            window.showToast(data.message, 'success');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        if (window.showToast) window.showToast('Reorder save failed.', 'error');
                    });
                }
            });
        }
    }
})();

