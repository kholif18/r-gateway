@extends('components.app')

@section('title', 'Login')

@section('content')
    <div class="qr-container">
        <h2>Login WhatsApp</h2>
        <p>Scan QR Code untuk menghubungkan akun WhatsApp Anda</p>
        
        <div class="row mb-4">
            <div class="col-12 col-md-6">
                <div class="qr-box">
                    <div class="qr-placeholder text-center p-4" id="qr-code-container">
                        <!-- QR code akan muncul di sini -->

                    </div>
                    <p>QR Code akan diperbarui setiap 60 detik</p>
                </div>
                <button id="start-session" class="btn btn-success mb-3">
                    <i class="fas fa-play"></i> Mulai Sesi
                </button>
                <button id="refresh-qr" class="btn btn-primary mt-2">
                    <i class="fas fa-sync"></i> Generate New QR Code
                </button>
            </div>
            <div class="col-12 col-md-6">
                <div class="qr-instructions">
                    <h3>Petunjuk Penggunaan:</h3>
                    <ol>
                        <li>Buka aplikasi WhatsApp di ponsel Anda</li>
                        <li>Klik menu titik tiga (⋮) di pojok kanan atas</li>
                        <li>Pilih "Linked Devices"</li>
                        <li>Klik "Link a Device"</li>
                        <li>Arahkan kamera ponsel Anda ke QR Code di atas</li>
                        <li>Tunggu hingga proses koneksi selesai</li>
                    </ol>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Status Koneksi</div>
            </div>
            <div class="card-body">
                <p>Status: <span id="status-text" style="color: var(--primary); font-weight: bold;">Memeriksa status...</span></p>
                <p id="last-connected" style="margin-top: 10px;"></p>
            </div>
            <!-- Form Logout WhatsApp -->
            <div class="card-footer" id="whatsapp-logout" style="display: none;">
                <form method="POST" action="{{ route('whatsapp.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout WhatsApp
                    </button>
                </form>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const qrContainer = document.getElementById('qr-code-container');
        const refreshBtn = document.getElementById('refresh-qr');
        const statusText = document.getElementById('status-text');
        const logoutSection = document.getElementById("whatsapp-logout");

        document.getElementById('start-session').addEventListener('click', async function () {
            const res = await fetch("{{ route('whatsapp.start') }}");
            const data = await res.json();
            alert(data.message || data.error);
        });

        async function loadQr() {
            qrContainer.innerHTML = `<div class="text-center p-4">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p>Loading QR Code...</p>
            </div>`;

            try {
                const res = await fetch(`{{ route('whatsapp.qr') }}`);
                const data = await res.json();

                if (data.qr) {
                    qrContainer.innerHTML = `<img src="${data.qr}" class="img-fluid" alt="QR Code">`;
                } else if (data.error) {
                    qrContainer.innerHTML = `<p>${data.error}</p>`;
                }
            } catch (err) {
                qrContainer.innerHTML = `<p>Gagal memuat QR</p>`;
                console.error('QR Error:', err);
            }
        }

        async function checkStatus() {
            try {
                const res = await fetch('{{ route("whatsapp.status") }}');
                const data = await res.json();
                
                statusText.innerHTML = data.status === 'CONNECTED' 
                    ? `<span class="text-success">Connected</span>` 
                    : `<span class="text-warning">${data.status || 'Disconnected'}</span>`;
                    
                if (data.status === 'CONNECTED') {
                    document.getElementById('whatsapp-logout').style.display = 'block';
                } else {
                    document.getElementById('whatsapp-logout').style.display = 'none';
                }
            } catch (error) {
                statusText.innerHTML = `<span class="text-danger">Status check failed</span>`;
            }
        }

        // Inisialisasi pertama
        loadQr();
        checkStatus();
        
        // Auto-refresh setiap 20 detik
        const interval = setInterval(() => {
            checkStatus();
            if (!document.querySelector('#qr-code-container img')) {
                loadQr();
            }
        }, 60000);

        // Manual refresh
        refreshBtn.addEventListener('click', () => {
            clearInterval(interval);
            loadQr();
            checkStatus();
        });
    });
</script>


@endsection