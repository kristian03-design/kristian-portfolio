<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel | Kristian Hernandez</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/chibi-logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">
    
    <!-- Prevent dark theme flash -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body>

<div class="layout">
    <!-- Sidebar Navigation -->
    @include('admin.partials.sidebar')

    <!-- Main Content Panel -->
    <main class="main-content">
        <!-- Header / Topbar -->
        @include('admin.partials.topbar')

        <!-- Dynamic Content Tabs -->
        <div class="content-wrapper">
            @include('admin.partials.tab-dashboard')
            @include('admin.partials.tab-projects')
            @include('admin.partials.tab-skills')
            @include('admin.partials.tab-experience')
            @include('admin.partials.tab-certifications')
            @include('admin.partials.tab-gallery')
            @include('admin.partials.tab-messages')
            @include('admin.partials.tab-profile')
        </div>
    </main>
</div>

<!-- Project Case Study Details Drawer Overlay -->
@include('admin.partials.drawer')

<!-- Message Email Reply Modal Overlay -->
@include('admin.partials.modal')

<!-- Core Admin Script Bindings -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Theme toggler action
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            });
        }

        // Sidebar collapsible persistent action
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarIcon = document.getElementById('sidebar-toggle-icon');
        if (sidebar && sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebar_collapsed', isCollapsed);
                
                // Toggle icon
                if (isCollapsed) {
                    sidebarIcon.className = 'ti ti-layout-sidebar-left-expand';
                } else {
                    sidebarIcon.className = 'ti ti-layout-sidebar-left-collapse';
                }
            });

            // Restore state
            if (localStorage.getItem('sidebar_collapsed') === 'true') {
                sidebar.classList.add('collapsed');
                sidebarIcon.className = 'ti ti-layout-sidebar-left-expand';
            }
        }

        // Toasts Triggering from Session Flash (Sonner)
        if (window.toast) {
            @if(session('success'))
                window.toast.success(@json(session('success')));
            @endif

            @if(session('status') === 'password-updated')
                window.toast.success('Password updated successfully.');
            @elseif(session('status'))
                window.toast.info(@json(session('status')));
            @endif

            @if(session('warning'))
                window.toast.warning(@json(session('warning')));
            @endif

            @if(session('error'))
                window.toast.error(@json(session('error')));
            @endif

            @if($errors->any())
                window.toast.error(@json($errors->first()));
            @endif
        }
    });
</script>

</body>
</html>
