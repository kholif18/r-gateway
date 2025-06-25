<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <a href="{{ url('/') }}">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI2ZmZiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxwYXRoIGQ9Ik0xNyA0aDNhMiAyIDAgMCAxIDIgMnYxNGEyIDIgMCAwIDEtMiAySDRhMiAyIDAgMCAxLTItMlY2YTIgMiAwIDAgMSAyLTJoNyI+PC9wYXRoPjxwYXRoIGQ9Ik0xNyAxN1Y0YTIgMiAwIDAgMC0yLTJIOGEyIDIgMCAwIDAtMiAydjEzYTIgMiAwIDAgMCAyIDJoN2EyIDIgMCAwIDAgMi0yeiI+PC9wYXRoPjwvc3ZnPg==" alt="Logo">
                <h2>WA Gateway</h2>
            </a>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <a href="{{ url('/') }}" class="menu-item {{ Request::is('/') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('whatsapp.message') }}" class="menu-item  {{ Route::is('whatsapp.message') ? 'active' : '' }}">
            <i class="fas fa-paper-plane"></i>
            <span>Send Message</span>
        </a>   
        
        <a href="{{ route('login.whatsapp') }}" class="menu-item  {{ Route::is('login.whatsapp') ? 'active' : '' }}">
            <i class="fas fa-qrcode"></i>
            <span>Login WhatsApp</span>
        </a>

        <div class="divider"></div>
        

        <a href="{{ route('user') }}" class="menu-item  {{ Request::is('user') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
        </a>

        <a href="{{ route('history') }}" class="menu-item  {{ Request::is('history') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>History</span>
        </a>

        <a href="{{ route('report') }}" class="menu-item  {{ Request::is('report') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Report</span>
        </a>
        
        <div class="divider"></div>

        <a href="{{ route('settings') }}" class="menu-item  {{ Request::is('settings') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>

    </div>
</div>