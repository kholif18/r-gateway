<div class="sidebar">
    <div class="sidebar-header">
        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiNmZmYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cGF0aCBkPSJNMTcgNGgzYTIgMiAwIDAgMSAyIDJ2MTRhMiAyIDAgMCAxLTIgMkg0YTIgMiAwIDAgMS0yLTJWNmEyIDIgMCAwIDEgMi0yaDciPjwvcGF0aD48cGF0aCBkPSJNMTcgMTdWNGEyIDIgMCAwIDAtMi0ySDhhMiAyIDAgMCAwLTIgMnYxM2EyIDIgMCAwIDAgMiAyaDdhMiAyIDAgMCAwIDItMnoiPjwvcGF0aD48L3N2Zz4=" alt="Logo">
        <h2>WA-Gateway</h2>
    </div>
    
    <div class="sidebar-menu">
        <a href="{{ url('/') }}" class="menu-item {{ Request::is('/') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ url('/send-message') }}" class="menu-item  {{ Request::is('send-message') ? 'active' : '' }}">
            <i class="fas fa-paper-plane"></i>
            <span>Send Message</span>
        </a>   
        
        <a href="{{ url('/wa-login') }}" class="menu-item  {{ Request::is('wa-login') ? 'active' : '' }}">
            <i class="fas fa-qrcode"></i>
            <span>Login WhatsApp</span>
        </a>

        <div class="divider"></div>
        

        <a href="{{ url('/user') }}" class="menu-item  {{ Request::is('user') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
        </a>

        <a href="{{ url('/history') }}" class="menu-item  {{ Request::is('history') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>History</span>
        </a>

        <a href="{{ url('/report') }}" class="menu-item  {{ Request::is('report') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Report</span>
        </a>
        
        <div class="divider"></div>

        <a href="{{ url('/settings') }}" class="menu-item  {{ Request::is('settings') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>

    </div>
</div>