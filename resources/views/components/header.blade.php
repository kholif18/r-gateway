<div class="header">
    <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <div class="header-title">@yield('title')</div>
    <div class="header-actions">
        <!-- Avatar Dropdown (Tanpa JavaScript) -->
        <div class="avatar-container">
            <div class="avatar">AD</div>
            <div class="dropdown-menu">
                <a href="{{ url('/user') }}" class="dropdown-item">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>
                <a href="{{ url('/settings')}}" class="dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <div class="divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </div>
    </div>
</div>