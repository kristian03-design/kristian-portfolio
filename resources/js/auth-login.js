function loadLucide() {
    if (window.lucide) {
        window.lucide.createIcons();
        return;
    }

    const script = document.createElement('script');
    script.src = 'https://unpkg.com/lucide@latest';
    script.onload = () => window.lucide?.createIcons();
    document.head.appendChild(script);
}

loadLucide();
