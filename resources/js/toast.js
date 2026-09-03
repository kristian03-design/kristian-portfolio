import toast from 'sonner-js';

// Centralized Sonner Configuration & Wrapper
const recentToasts = new Map();
const DEDUPE_WINDOW_MS = 1500;

function isDuplicate(message, type) {
    const key = `${type}:${message}`;
    const now = Date.now();
    const lastTime = recentToasts.get(key);
    if (lastTime && now - lastTime < DEDUPE_WINDOW_MS) {
        return true;
    }
    recentToasts.set(key, now);
    // Cleanup stale entries
    if (recentToasts.size > 50) {
        for (const [k, time] of recentToasts.entries()) {
            if (now - time > 10000) recentToasts.delete(k);
        }
    }
    return false;
}

function getSystemTheme() {
    const dataTheme = document.documentElement.getAttribute('data-theme');
    if (dataTheme === 'dark' || dataTheme === 'light') {
        return dataTheme;
    }
    if (document.documentElement.classList.contains('dark')) {
        return 'dark';
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

// Initialize Sonner configuration
function configureSonner() {
    const currentTheme = getSystemTheme();
    toast.config({
        theme: currentTheme,
        position: 'bottom-right',
        richColors: true,
        closeButton: true,
        duration: 4000,
        gap: 12,
        offset: 24,
        mobileOffset: 16,
    });
}

configureSonner();

// Watch for theme changes (e.g. data-theme attribute or class toggle)
const themeObserver = new MutationObserver(() => {
    configureSonner();
});

themeObserver.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['data-theme', 'class'],
});

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    configureSonner();
});

// Wrapped Sonner methods with deduplication and accessibility
const safeToast = {
    ...toast,
    success(message, options = {}) {
        if (!message || isDuplicate(message, 'success')) return;
        return toast.success(message, {
            ...options,
        });
    },
    error(message, options = {}) {
        if (!message || isDuplicate(message, 'error')) return;
        return toast.error(message, {
            ...options,
        });
    },
    warning(message, options = {}) {
        if (!message || isDuplicate(message, 'warning')) return;
        return toast.warning(message, {
            ...options,
        });
    },
    info(message, options = {}) {
        if (!message || isDuplicate(message, 'info')) return;
        return toast.info(message, {
            ...options,
        });
    },
    loading(message, options = {}) {
        return toast.loading(message, options);
    },
    dismiss(id) {
        toast.dismiss(id);
    },
    promise: toast.promise,
    config: toast.config,
    /**
     * Dispatch server-sent flash messages seamlessly.
     */
    flash({ success, error, warning, info, status } = {}) {
        if (success) {
            safeToast.success(success);
        }
        if (error) {
            safeToast.error(error);
        }
        if (warning) {
            safeToast.warning(warning);
        }
        if (info) {
            safeToast.info(info);
        }
        if (status && status !== success) {
            safeToast.info(status);
        }
    }
};

// Expose globally for both module and inline Blade use
window.toast = safeToast;

// Backward-compatibility bridge for any existing window.showToast calls
window.showToast = function(message, type = 'success') {
    if (!message) return;
    if (type === 'success') {
        safeToast.success(message);
    } else if (type === 'error' || type === 'danger') {
        safeToast.error(message);
    } else if (type === 'warning') {
        safeToast.warning(message);
    } else {
        safeToast.info(message);
    }
};

export default safeToast;
