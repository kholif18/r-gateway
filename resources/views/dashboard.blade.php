@extends('components.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-overview">
        <!-- Status Gateway Card -->
        <div class="status-card status-unknown" id="gateway-status-card">
            <div class="card-icon">
                <i class="fas fa-plug"></i>
            </div>
            <div class="card-value"  id="gateway-status-text">Memeriksa...</div>
            <div class="card-title">Status Gateway</div>
            <div class="card-badge status-badge-unknown" id="gateway-status-badge">
                <i class="fas  fa-spinner fa-spin"></i> Mengecek...
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
        <a href="{{ route('whatsapp.message') }}" class="action-btn" data-page="send-message">
            <i class="fas fa-paper-plane"></i>
            <div class="btn-title">Send Message</div>
            <div class="btn-desc">Test Send Message</div>
        </a>
        
        <a href="{{ route('history') }}" class="action-btn" data-page="history">
            <i class="fas fa-history"></i>
            <div class="btn-title">History</div>
            <div class="btn-desc">Check History Send</div>
        </a>
        
        <a href="{{ route('login.whatsapp') }}" class="action-btn" data-page="contacts">
            <i class="fas fa-qrcode"></i>
            <div class="btn-title">Status Device</div>
            <div class="btn-desc">Check Status Device</div>
        </a>
        
        <a href="{{ route('settings') }}" class="action-btn" data-page="auto-reply">
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
            <div class="message-avatar">{{ strtoupper(substr($lastMessage->client_name, 0, 2)) }}</div>
            <div class="message-content">
                <div class="message-header">
                    <div class="message-sender">{{ $lastMessage->client_name }}</div>
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

    <script>
        // document.addEventListener("DOMContentLoaded", function () {
        //     fetch("{{ route('dashboard.status') }}")
        //         .then(response => response.json())
        //         .then(data => {
        //             const card = document.getElementById('gateway-status-card');
        //             const text = document.getElementById('gateway-status-text');
        //             const badge = document.getElementById('gateway-status-badge');

        //             if (data.connected) {
        //                 card.classList.remove('status-unknown', 'status-disconnected');
        //                 card.classList.add('status-connected');

        //                 badge.className = 'card-badge status-badge-connected';
        //                 badge.innerHTML = '<i class="fas fa-check-circle"></i> Aktif';
        //                 text.textContent = 'Terhubung';
        //             } else {
        //                 card.classList.remove('status-unknown', 'status-connected');
        //                 card.classList.add('status-disconnected');

        //                 badge.className = 'card-badge status-badge-disconnected';
        //                 badge.innerHTML = '<i class="fas fa-exclamation-circle"></i> Tidak Aktif';
        //                 text.textContent = 'Terputus';
        //             }
        //         })
        //         .catch(error => {
        //             console.error('Gagal memuat status gateway:', error);
        //             const card = document.getElementById('gateway-status-card');
        //             const text = document.getElementById('gateway-status-text');
        //             const badge = document.getElementById('gateway-status-badge');

        //             card.classList.remove('status-connected');
        //             card.classList.add('status-disconnected');
        //             badge.className = 'card-badge status-badge-disconnected';
        //             badge.innerHTML = '<i class="fas fa-times-circle"></i> Error';
        //             text.textContent = 'Terputus';
        //         });
        // });
        document.addEventListener("DOMContentLoaded", function () {
            fetch("{{ route('dashboard.status') }}")
                .then(response => response.json())
                .then(data => {
                    const card = document.getElementById('gateway-status-card');
                    const text = document.getElementById('gateway-status-text');
                    const badge = document.getElementById('gateway-status-badge');

                    if (data.connected) {
                        card.classList.remove('status-unknown', 'status-disconnected');
                        card.classList.add('status-connected');

                        badge.className = 'card-badge status-badge-connected';
                        badge.innerHTML = '<i class="fas fa-check-circle"></i> Aktif';
                        text.textContent = 'Terhubung';
                    } else {
                        card.classList.remove('status-unknown', 'status-connected');
                        card.classList.add('status-disconnected');

                        badge.className = 'card-badge status-badge-disconnected';
                        badge.innerHTML = '<i class="fas fa-exclamation-circle"></i> Tidak Aktif';
                        text.textContent = 'Terputus';
                    }
                })
                .catch(error => {
                    console.error('Gagal memuat status gateway:', error);
                    const card = document.getElementById('gateway-status-card');
                    const text = document.getElementById('gateway-status-text');
                    const badge = document.getElementById('gateway-status-badge');

                    card.classList.remove('status-connected');
                    card.classList.add('status-disconnected');
                    badge.className = 'card-badge status-badge-disconnected';
                    badge.innerHTML = '<i class="fas fa-times-circle"></i> Error';
                    text.textContent = 'Terputus';
                });
        });
    </script>
@endsection
