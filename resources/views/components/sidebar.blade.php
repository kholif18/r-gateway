<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logo-white.png') }}" alt="Logo">
                <h2>r gateway</h2>
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
        
        <a href="{{ route('whatsapp.login') }}" class="menu-item  {{ Route::is('whatsapp.login') ? 'active' : '' }}">
            <i class="fas fa-qrcode"></i>
            <span>Login WhatsApp</span>
        </a>

        <div class="divider"></div>
        
        <a href="{{ route('history') }}" class="menu-item  {{ Request::is('history') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>History</span>
        </a>

        <a href="{{ route('report') }}" class="menu-item  {{ Request::is('report') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Report</span>
        </a>

        <a href="{{ route('logs.index') }}" class="menu-item  {{ Route::is('logs.index') ? 'active' : '' }}">
            <i class="fas fa-file-lines"></i>
            <span>Logs</span>
        </a>
        
        <div class="divider"></div>

        <a href="{{ route('user') }}" class="menu-item  {{ Request::is('user') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
        </a>

        <a href="{{ route('clients.index') }}" class="menu-item  {{ Route::is('clients.index') ? 'active' : '' }}">
            <i class="fas fa-key"></i>
            <span>API Client</span>
        </a>

        <a href="{{ route('settings') }}" class="menu-item  {{ Request::is('settings') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>

        <a href="{{ route('help.index') }}" class="menu-item  {{ Route::is('help.index*') ? 'active' : '' }}">
            <i class="fas fa-question-circle"></i>
            <span>Help</span>
        </a>
    </div>
</div>