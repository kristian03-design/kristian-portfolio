const tabTitles = {
    dashboard: 'Dashboard',
    projects: 'Projects',
    skills: 'Skills',
    experience: 'Experience',
    certifications: 'Certifications',
    messages: 'Messages',
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
    const activeTab = localStorage.getItem('admin_active_tab');
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

document.addEventListener('DOMContentLoaded', () => {
    const skillSelect = document.getElementById('skill-name-select');
    const categoryDisplay = document.getElementById('skill-category-display');

    if (!skillSelect || !categoryDisplay) return;

    function syncSkillCategory() {
        const selected = skillSelect.selectedOptions[0];
        categoryDisplay.value = selected?.dataset.category || 'Select a skill first';
    }

    skillSelect.addEventListener('change', syncSkillCategory);
    syncSkillCategory();
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

    function handleFile(file) {
        warningAlert.style.display = 'none';
        
        if (file.type === 'application/pdf' || file.name.endsWith('.pdf')) {
            handlePDF(file);
        } else if (file.type.match('image.*')) {
            handleImage(file);
        } else {
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
                
                // Configure PDF.js worker
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
                
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
