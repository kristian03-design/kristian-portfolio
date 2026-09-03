import '../css/loading.css';
import toast from './toast';
import { setButtonLoading, resetButtonLoading, resetFormLoading } from './loading';

// Global Password Toggle for Auth views
window.togglePass = function(inputId = 'password', iconId = 'password-toggle-icon') {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.replace('ti-eye', 'ti-eye-off');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.replace('ti-eye-off', 'ti-eye');
        }
    }
};

// Lucide icon helper
function loadLucide() {
    if (typeof window.initLucideIcons === 'function') {
        window.initLucideIcons();
    } else if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadLucide();

    // Auto-surface server flash messages to Sonner if not already shown
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('reset') === 'success') {
        toast.success('Your password has been reset successfully.');
    }
});

export default { toast, setButtonLoading, resetButtonLoading };
