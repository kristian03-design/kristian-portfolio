// Universal Loading State & Double-Submit Protection System

/**
 * Determine contextual loading text based on button label or explicit data attribute.
 */
export function getContextualLoadingText(button) {
    if (button.dataset.loadingText) {
        return button.dataset.loadingText;
    }
    const text = button.textContent.trim().toLowerCase();
    if (text.includes('sign in') || text.includes('log in') || text.includes('signin') || text.includes('login')) {
        return 'Signing in...';
    }
    if (text.includes('verify')) {
        return 'Verifying...';
    }
    if (text.includes('save') || text.includes('create') || text.includes('add')) {
        return 'Saving...';
    }
    if (text.includes('update') || text.includes('edit')) {
        return 'Updating...';
    }
    if (text.includes('send') || text.includes('reply')) {
        return 'Sending...';
    }
    if (text.includes('delete') || text.includes('remove')) {
        return 'Deleting...';
    }
    if (text.includes('upload')) {
        return 'Uploading...';
    }
    if (text.includes('scan') || text.includes('ocr')) {
        return 'Scanning...';
    }
    if (text.includes('reset')) {
        return 'Resetting...';
    }
    return 'Processing...';
}

/**
 * Puts a button into a locked loading state with a spinner, preserving its exact layout dimensions.
 */
export function setButtonLoading(button, customText = null) {
    if (!button || button.classList.contains('is-loading')) return;

    // Prevent layout shift: Lock existing rendered dimensions
    const rect = button.getBoundingClientRect();
    if (rect.width > 0) {
        button.style.minWidth = `${rect.width}px`;
    }
    if (rect.height > 0) {
        button.style.minHeight = `${rect.height}px`;
    }

    // Cache original HTML
    button.dataset.originalHtml = button.innerHTML;
    button.classList.add('is-loading');
    button.setAttribute('aria-busy', 'true');
    button.disabled = true;

    const loadingText = customText || getContextualLoadingText(button);

    button.innerHTML = `
        <span class="btn-spinner-icon" aria-hidden="true">
            <svg class="animate-spin" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor"></path>
            </svg>
        </span>
        <span class="btn-loading-text">${loadingText}</span>
    `;
}

/**
 * Restores a button from its loading state to its original content and dimensions.
 */
export function resetButtonLoading(button) {
    if (!button) return;

    if (button.dataset.originalHtml) {
        button.innerHTML = button.dataset.originalHtml;
        delete button.dataset.originalHtml;
    }

    button.style.removeProperty('min-width');
    button.style.removeProperty('min-height');
    button.classList.remove('is-loading');
    button.removeAttribute('aria-busy');
    button.disabled = false;
}

/**
 * Restores all loading buttons and state within a form.
 */
export function resetFormLoading(form) {
    if (!form) return;

    form.classList.remove('is-submitting');

    // Restore submit buttons
    const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
    submitBtns.forEach(btn => resetButtonLoading(btn));

    // Also check buttons linked via form attribute
    if (form.id) {
        document.querySelectorAll(`button[type="submit"][form="${form.id}"]`).forEach(btn => resetButtonLoading(btn));
    }

    // Re-enable cancel/ghost buttons
    form.querySelectorAll('button[type="reset"], .btn-ghost, .js-cancel-btn').forEach(btn => {
        btn.style.removeProperty('pointer-events');
        btn.style.removeProperty('opacity');
    });

    const formName = form.id ? form.id.split('-')[0] : '';
    if (formName) {
        document.querySelectorAll(`#${formName}-edit-cancel, #${formName}-cancel-btn`).forEach(btn => {
            btn.style.removeProperty('pointer-events');
            btn.style.removeProperty('opacity');
        });
    }
}

// Global Form Submit Listener
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;

    // Ignore if opting out
    if (form.hasAttribute('data-no-loading') || form.classList.contains('no-loading')) {
        return;
    }

    // If already submitting, prevent duplicate submission
    if (form.classList.contains('is-submitting')) {
        e.preventDefault();
        return;
    }

    // If HTML5 validation fails, do not lock the form
    if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
        return;
    }

    // Locate the submit button
    let submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
    if (!submitBtn && form.id) {
        submitBtn = document.querySelector(`button[type="submit"][form="${form.id}"], input[type="submit"][form="${form.id}"]`);
    }

    if (!submitBtn) return;

    // Mark as submitting
    form.classList.add('is-submitting');
    setButtonLoading(submitBtn);

    // Disable cancel/reset buttons inside form to prevent conflicting states
    form.querySelectorAll('button[type="reset"], .btn-ghost, .js-cancel-btn').forEach(btn => {
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.5';
    });

    const formName = form.id ? form.id.split('-')[0] : '';
    if (formName) {
        document.querySelectorAll(`#${formName}-edit-cancel, #${formName}-cancel-btn`).forEach(btn => {
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.5';
        });
    }
});

// Browser History / bfcache Restoration Handler
// If the user navigates back to the page, ensure buttons are never permanently stuck in a loading state.
window.addEventListener('pageshow', (event) => {
    document.querySelectorAll('form.is-submitting').forEach(form => {
        resetFormLoading(form);
    });
    document.querySelectorAll('button.is-loading').forEach(btn => {
        resetButtonLoading(btn);
    });
});

// Expose utilities on window
window.setButtonLoading = setButtonLoading;
window.resetButtonLoading = resetButtonLoading;
window.resetFormLoading = resetFormLoading;
