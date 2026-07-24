function loadLucide() {
    if (typeof window.initLucideIcons === 'function') {
        window.initLucideIcons();
    } else if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
}

document.addEventListener('DOMContentLoaded', loadLucide);
window.addEventListener('load', loadLucide);
loadLucide();
