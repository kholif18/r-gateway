<div class="header">
    <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <div class="header-title">@yield('title')</div>
    <div class="header-actions">
        <!-- Avatar Dropdown (Tanpa JavaScript) -->
        <div class="avatar-container">
            <div class="avatar">
                <img src="{{ $user->avatar_url ?? auth()->user()->avatar_url }}" alt="Avatar">
            </div>
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
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>