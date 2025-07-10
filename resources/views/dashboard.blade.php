@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-overview">
        <!-- Status Gateway Card -->
        <div class="status-card status-unknown" id="gateway-status-card">
            <div class="card-icon">
                <i class="fas fa-plug"></i>
            </div>
            <div class="card-value"  id="gateway-status-text">Checking...</div>
            <div class="card-title">Status Gateway</div>
            <div class="card-badge status-badge-unknown" id="gateway-status-badge">
                <i class="fas fa-spinner fa-spin"></i> Checking...
            </div>
        </div>
        
        <!-- Pesan Terkirim Hari Ini Card -->
        <div class="status-card status-messages">
            <div class="card-icon">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="card-value">{{ $sentToday }}</div>
            <div class="card-title">Message sent today</div>
            <div class="card-badge 
                {{ $growthDirection === 'up' ? 'status-badge-connected' : 
                ($growthDirection === 'down' ? 'status-badge-disconnected' : 'status-badge-unknown') }}">
                <i class="fas fa-arrow-{{ $growthDirection }}"></i> {{ $sentTodayGrowth }}%
            </div>
        </div>
        
        <!-- Statistik Lainnya Card -->
        <div class="status-card" style="background: {{ $successBackground }};">
            <div class="card-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="card-value">{{ $successRate }}%</div>
            <div class="card-title">Success rate</div>
            <div class="card-badge {{ $successBadge }}">
                <i class="fas {{ $successIcon }}"></i> {{ $successStatus }}
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
        
        <a href="{{ route('whatsapp.login') }}" class="action-btn" data-page="contacts">
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
            <div class="last-message-title">Last Message</div>
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
                @php
                    $status = strtolower($lastMessage->status);
                    [$statusClass, $icon] = match($status) {
                        'failed' => ['status-failed', 'fas fa-times-circle'],
                        'pending' => ['status-pending', 'fas fa-clock'],
                        default => ['status-delivered', 'fas fa-check-circle'],
                    };
                @endphp

                <div class="message-status {{ $statusClass }}">
                    <i class="{{ $icon }}"></i> {{ ucfirst($status) }}
                </div>
            </div>
        </div>
        @else
            <div class="text-center">No messages have been sent yet.</div>
        @endif
    </div>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="feedbackToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div id="toastHeader" class="toast-header">
                <strong class="me-auto" id="toastTitle"></strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastBody"></div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const card = document.getElementById('gateway-status-card');
            const text = document.getElementById('gateway-status-text');
            const badge = document.getElementById('gateway-status-badge');

            function updateStatusUI(data) {
                if (data.connected) {
                    card.classList.remove('status-unknown', 'status-disconnected');
                    card.classList.add('status-connected');

                    badge.className = 'card-badge status-badge-connected';
                    badge.innerHTML = '<i class="fas fa-check-circle"></i> Active';
                    text.textContent = 'Connected';
                } else {
                    card.classList.remove('status-unknown', 'status-connected');
                    card.classList.add('status-disconnected');

                    badge.className = 'card-badge status-badge-disconnected';
                    badge.innerHTML = '<i class="fas fa-exclamation-circle"></i> Not active';
                    text.textContent = 'Disconnected';
                }
            }

            function showError() {
                card.classList.remove('status-connected', 'status-unknown');
                card.classList.add('status-disconnected');
                badge.className = 'card-badge status-badge-disconnected';
                badge.innerHTML = '<i class="fas fa-times-circle"></i> Error';
                text.textContent = 'Disconnected';
            }

            function fetchGatewayStatus() {
                fetch("{{ route('dashboard.status') }}")
                    .then(response => response.json())
                    .then(data => updateStatusUI(data))
                    .catch(error => {
                        console.error('Failed to load gateway status:', error);
                        showError();
                    });
            }

            // Fetch saat pertama kali halaman dimuat
            setTimeout(fetchGatewayStatus, 100);

            // Set interval refresh setiap 30 detik
            setInterval(fetchGatewayStatus, 30000);

            function showToast(title, message, type = 'success') {
                const toastEl = document.getElementById('feedbackToast');
                const toast = new bootstrap.Toast(toastEl);

                const titleEl = document.getElementById('toastTitle');
                const bodyEl = document.getElementById('toastBody');
                const header = document.getElementById('toastHeader');

                header.className = 'toast-header';
                toastEl.classList.remove('bg-success', 'bg-danger');

                titleEl.innerText = title;
                bodyEl.innerHTML = message;

                if (type === 'success') {
                    header.classList.add('bg-success', 'text-white');
                    toastEl.classList.add('bg-success', 'text-white');
                } else {
                    header.classList.add('bg-warning', 'text-dark');
                    toastEl.classList.add('bg-warning', 'text-dark');
                }

                toast.show();
            }

            @if ($showUpdateToast)
                showToast(
                    'Pembaruan Tersedia',
                    `{!! 'Versi baru <strong>' . e($update['latest_version']) . '</strong> tersedia.<br>' .
                    (is_array($update['changelog']) ? implode('<br>', $update['changelog']) : e($update['changelog'])) .
                    '<br><a href="' . e($update['release_page']) . '" target="_blank" class="btn btn-sm btn-light mt-2">Lihat Update</a>' !!}`,
                    'warning'
                );
            @endif
        });
    </script>
@endsection
