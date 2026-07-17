<header class="topbar">
    <div class="topbar-left">
        <h1 class="topbar-title" id="page-title">Dashboard</h1>
    </div>

    <div class="topbar-right">
        <!-- Theme Toggle -->
        <button type="button" class="theme-toggle-btn" id="theme-toggle" title="Toggle Light/Dark Theme">
            <i class="ti ti-sun light-icon"></i>
            <i class="ti ti-moon dark-icon"></i>
        </button>

        <!-- Notification Bell -->
        <button type="button" class="notification-bell-btn" data-tab="messages" title="Go to Messages">
            <i class="ti ti-bell"></i>
            @if($unreadCount > 0)
                <span class="notification-dot"></span>
            @endif
        </button>

        <!-- Profile Menu -->
        <div class="user-menu" data-tab="profile" title="View Profile">
            <div class="user-avatar">
                {{ collect(explode(' ', auth()->user()->full_name))->map(fn($n) => $n[0] ?? '')->take(2)->join('') }}
            </div>
            <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary);" class="nav-text">{{ auth()->user()->full_name }}</span>
        </div>

        <!-- Log out -->
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-ghost btn-sm" title="Log Out" data-confirm="Are you sure you want to log out?">
                <i class="ti ti-logout" style="font-size: 15px;"></i>
            </button>
        </form>
    </div>
</header>
