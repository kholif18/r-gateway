@extends('components.app')

@section('title', 'Login')

@section('content')
<div class="qr-container">
        <h2>Login WhatsApp</h2>
        <p>Scan QR Code untuk menghubungkan akun WhatsApp Anda</p>
        
        <div class="row mb-4">
            <div class="col-12 col-md-6">
                <div class="qr-box">
                    <div class="qr-placeholder">
                        <img src="data:image/png;base64,{{ $qr }}" alt="QR Code">
                    <p>QR Code akan diperbarui setiap 60 detik</p>
                </div>
                <div id="status-message" class="alert alert-info">
                    Preparing QR code...
                </div>
                <button id="refresh-btn" class="btn">
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
                <p>Status: <span style="color: var(--primary); font-weight: bold;">Menunggu Scan QR Code</span></p>
                <p style="margin-top: 10px;">Terakhir terhubung: 23 Juni 2025, 14:30 WIB</p>
            </div>
<<<<<<< HEAD
            <form id="logout-form" style="display: none;" method="POST" action="{{ route('logout') }}">
=======
            <form method="POST" action="{{ route('whatsapp.logout') }}" id="logout-form" style="display: none;">
>>>>>>> 46d8188ffa3dba89c3306f41ef99763997932259
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </button>
            </form>

        </div>
    </div>

<<<<<<< HEAD
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Panggil semua fungsi ini di sini
=======
    {{-- <script>
        function fetchStatus() {
            fetch('/whatsapp/status')
                .then(res => res.json())
                .then(data => {
                    const statusText = document.querySelector('.card-body span');
                    const lastConnected = document.querySelector('.card-body p:last-child');
                    const logoutForm = document.getElementById('logout-form');
>>>>>>> 46d8188ffa3dba89c3306f41ef99763997932259

            function fetchStatus() {
                fetch('/whatsapp/status')
                    .then(res => res.json())
                    .then(data => {
                        const statusText = document.querySelector('.card-body span');
                        const lastConnected = document.querySelector('.card-body p:last-child');
                        const logoutForm = document.getElementById('logout-form');

                        if (data.status === 'CONNECTED') {
                            statusText.textContent = "Terhubung ke WhatsApp";
                            statusText.style.color = 'green';
                            document.querySelector('.qr-box').style.display = 'none';
                            logoutForm.style.display = 'block';
                            lastConnected.textContent = 'Terakhir terhubung: ' + new Date().toLocaleString();
                        } else {
                            statusText.textContent = "Menunggu Scan QR Code";
                            statusText.style.color = 'orange';
                            document.querySelector('.qr-box').style.display = 'block';
                            logoutForm.style.display = 'none';
                        }
                    });
            }

            function fetchQR() {
                fetch('/whatsapp/qr')
                    .then(res => res.json())
                    .then(data => {
                        console.log('RESPON QR:', data);
                        const qrImg = document.querySelector('.qr-placeholder');
                        if (data.qr) {
                            qrImg.innerHTML = `<img src="data:image/png;base64,${data.qr}" alt="QR Code" style="max-width: 100%;">`;
                        } else {
                            qrImg.innerHTML = '<p>QR tidak tersedia</p>';
                        }
                    });
            }

            // Panggil pertama kali
            fetchStatus();
            fetchQR();

<<<<<<< HEAD
            // Jalankan setiap 5 detik
            setInterval(() => {
                fetchStatus();
                fetchQR();
            }, 5000);
        });
        </script>

=======
        // Panggil pertama kali
        fetchStatus();
        fetchQR();
    </script> --}}
    {{-- <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Login WhatsApp</h3>
                    </div>

                    <div class="card-body">
                        <div class="text-center mb-4">
                            <p class="lead">Scan QR Code untuk menghubungkan akun WhatsApp Anda</p>
                        </div>
                        
                        <div class="row">
                            <!-- QR Code Section -->
                            <div class="col-md-6 mb-4 mb-md-0">
                                <div class="border p-3 text-center">
                                    <div id="qr-container" class="mb-3">
                                        <div id="qr-image-placeholder" class="d-flex justify-content-center align-items-center" style="height: 250px; background-color: #f8f9fa;">
                                            <div class="text-center">
                                                <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                                                <p class="mt-2">Memuat QR Code...</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="qr-message" class="alert alert-info mb-3">
                                        QR Code akan diperbarui setiap 60 detik
                                    </div>
                                    <button id="refresh-btn" class="btn btn-primary">
                                        <i class="fas fa-sync-alt mr-2"></i> Generate QR Code Baru
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Instructions Section -->
                            <div class="col-md-6">
                                <div class="border p-3 h-100">
                                    <h4 class="text-center mb-3">Petunjuk Penggunaan:</h4>
                                    <ol class="pl-3">
                                        <li class="mb-2">Buka aplikasi WhatsApp di ponsel Anda</li>
                                        <li class="mb-2">Klik menu titik tiga (⋮) di pojok kanan atas</li>
                                        <li class="mb-2">Pilih "Linked Devices"</li>
                                        <li class="mb-2">Klik "Link a Device"</li>
                                        <li class="mb-2">Arahkan kamera ponsel Anda ke QR Code di samping</li>
                                        <li class="mb-2">Tunggu hingga proses koneksi selesai</li>
                                    </ol>
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Jangan bagikan QR Code ini kepada siapapun!
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Connection Status -->
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Status Koneksi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1">Status: 
                                                <span id="connection-status" class="font-weight-bold" style="color: orange;">
                                                    Menunggu Scan QR Code
                                                </span>
                                            </p>
                                            <p class="mb-0 text-muted" id="last-connected">
                                                Terakhir terhubung: -
                                            </p>
                                        </div>
                                        <form method="POST" action="{{ route('whatsapp.logout') }}" id="logout-form" style="display: none;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <script>
        // Fungsi untuk memuat QR Code
        function fetchQRCode() {
            const qrPlaceholder = document.getElementById('qr-image-placeholder');
            const qrMessage = document.getElementById('qr-message');
            
            qrPlaceholder.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-2">Memuat QR Code...</p>
                </div>
            `;
            
            fetch('{{ route("whatsapp.qr") }}')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.qr) {
                        qrPlaceholder.innerHTML = `
                            <img src="${data.qr}" alt="WhatsApp QR Code" class="img-fluid">
                        `;
                        qrMessage.innerHTML = `
                            <i class="fas fa-info-circle mr-2"></i>
                            Scan QR Code ini dengan WhatsApp mobile Anda
                            <small class="d-block mt-1">Akan diperbarui dalam 60 detik</small>
                        `;
                    } else if (data.error) {
                        qrPlaceholder.innerHTML = `
                            <div class="text-center text-danger">
                                <i class="fas fa-exclamation-triangle fa-3x"></i>
                                <p class="mt-2">${data.error}</p>
                            </div>
                        `;
                        qrMessage.innerHTML = `
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Gagal memuat QR Code. Silakan coba lagi.
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching QR:', error);
                    qrPlaceholder.innerHTML = `
                        <div class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                            <p class="mt-2">Terjadi kesalahan</p>
                        </div>
                    `;
                    qrMessage.innerHTML = `
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Gagal terhubung ke server. Silakan refresh halaman.
                    `;
                });
        }

        // Fungsi untuk memeriksa status koneksi
        function checkConnectionStatus() {
            fetch('{{ route("whatsapp.status") }}')
                .then(response => response.json())
                .then(data => {
                    const statusElement = document.getElementById('connection-status');
                    const lastConnectedElement = document.getElementById('last-connected');
                    const logoutForm = document.getElementById('logout-form');
                    const qrContainer = document.getElementById('qr-container');

                    if (data.state === 'CONNECTED' || data.status === 'CONNECTED') {
                        statusElement.textContent = "Terhubung ke WhatsApp";
                        statusElement.style.color = 'green';
                        qrContainer.style.display = 'none';
                        logoutForm.style.display = 'block';
                        lastConnectedElement.textContent = 'Terakhir terhubung: ' + new Date().toLocaleString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            timeZoneName: 'short'
                        });
                    } else {
                        statusElement.textContent = "Menunggu Scan QR Code";
                        statusElement.style.color = 'orange';
                        qrContainer.style.display = 'block';
                        logoutForm.style.display = 'none';
                    }
                });
        }

        // Event listener untuk tombol refresh
        document.getElementById('refresh-btn').addEventListener('click', function(e) {
            e.preventDefault();
            fetchQRCode();
        });

        // Jalankan pertama kali
        fetchQRCode();
        checkConnectionStatus();

        // Auto-refresh setiap 5 detik
        setInterval(checkConnectionStatus, 5000);
        
        // Refresh QR code setiap 60 detik
        setInterval(fetchQRCode, 60000);
    </script>
>>>>>>> 46d8188ffa3dba89c3306f41ef99763997932259

@endsection