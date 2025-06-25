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
                        <!-- QR code akan muncul di sini -->
                    </div>
                    <p>QR Code akan diperbarui setiap 60 detik</p>
                </div>
                
                <button class="btn">
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
            <form id="logout-form" style="display: none;" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Panggil semua fungsi ini di sini

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

            // Jalankan setiap 5 detik
            setInterval(() => {
                fetchStatus();
                fetchQR();
            }, 5000);
        });
        </script>


@endsection