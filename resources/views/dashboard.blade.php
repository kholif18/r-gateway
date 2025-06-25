@extends('components.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-overview">
        <!-- Status Gateway Card -->
        <div class="status-card {{ $gatewayStatus ? 'status-connected' : 'status-disconnected' }}">
            <div class="card-icon">
                <i class="fas fa-plug"></i>
            </div>
            <div class="card-value">{{ $gatewayStatus ? 'Terhubung' : 'Terputus' }}</div>
            <div class="card-title">Status Gateway</div>
            <div class="card-badge {{ $gatewayStatus ? 'status-badge-connected' : 'status-badge-disconnected' }}">
                <i class="fas {{ $gatewayStatus ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                {{ $gatewayStatus ? 'Aktif' : 'Tidak Aktif' }}
            </div>
        </div>
        
        <!-- Pesan Terkirim Hari Ini Card -->
        <div class="status-card status-messages">
            <div class="card-icon">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="card-value">{{ $sentToday }}</div>
            <div class="card-title">Pesan Terkirim Hari Ini</div>
            <div class="card-badge status-badge-connected">
                <i class="fas fa-arrow-up"></i> {{ $sentTodayGrowth }}%
            </div>
        </div>
        
        <!-- Statistik Lainnya Card -->
        <div class="status-card" style="background: linear-gradient(135deg, var(--secondary), #054a43);">
            <div class="card-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="card-value">{{ $successRate }}%</div>
            <div class="card-title">Tingkat Keberhasilan</div>
            <div class="card-badge status-badge-connected">
                <i class="fas fa-chart-line"></i> Stabil
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ url('/send-message') }}" class="action-btn" data-page="send-message">
            <i class="fas fa-paper-plane"></i>
            <div class="btn-title">Send Message</div>
            <div class="btn-desc">Test Send Message</div>
        </a>
        
        <a href="{{ url('/history') }}" class="action-btn" data-page="history">
            <i class="fas fa-history"></i>
            <div class="btn-title">History</div>
            <div class="btn-desc">Check History Send</div>
        </a>
        
        <a href="{{ url('/wa-login') }}" class="action-btn" data-page="contacts">
            <i class="fas fa-qrcode"></i>
            <div class="btn-title">Status Device</div>
            <div class="btn-desc">Check Status Device</div>
        </a>
        
        <a href="{{ url('/settings') }}" class="action-btn" data-page="auto-reply">
            <i class="fas fa-cog"></i>
            <div class="btn-title">Settings</div>
            <div class="btn-desc">Setting your WA Gateway</div>
        </a>
    </div>
    
    <!-- Last Message Card -->
    <div class="last-message-card">
        <div class="last-message-header">
            <div class="last-message-title">Pesan Terakhir</div>
            <div class="last-message-time">{{ $lastMessage?->sent_at->format('d M Y, H:i') ?? '-' }}</div>
        </div>
        
        @if ($lastMessage)
        <div class="message-container">
            <div class="message-avatar">{{ strtoupper(substr($lastMessage->recipient_name, 0, 2)) }}</div>
            <div class="message-content">
                <div class="message-header">
                    <div class="message-sender">{{ $lastMessage->recipient_name }}</div>
                    <div class="message-time">{{ $lastMessage->sent_at->format('H:i') }}</div>
                </div>
                <div class="message-text">
                    {{ $lastMessage->message }}
                </div>
                <div class="message-status status-delivered">
                    <i class="fas fa-check-circle"></i> {{ ucfirst($lastMessage->status) }}
                </div>
            </div>
        </div>
        @else
            <div class="text-center">Belum ada pesan yang dikirim.</div>
        @endif
    </div>

    {{-- <script>
        // Simulate connection status change
        setInterval(() => {
            const statusCard = document.querySelector('.status-card.status-connected');
            if (Math.random() > 0.9) {
                statusCard.classList.remove('status-connected');
                statusCard.classList.add('status-disconnected');
                statusCard.querySelector('.card-value').textContent = 'Terputus';
                statusCard.querySelector('.card-badge').innerHTML = '<i class="fas fa-exclamation-circle"></i> Tidak Aktif';
            } else {
                statusCard.classList.remove('status-disconnected');
                statusCard.classList.add('status-connected');
                statusCard.querySelector('.card-value').textContent = 'Terhubung';
                statusCard.querySelector('.card-badge').innerHTML = '<i class="fas fa-check-circle"></i> Aktif';
            }
        }, 10000);
    </script> --}}
@endsection
