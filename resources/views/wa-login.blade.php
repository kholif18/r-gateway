@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="qr-container">
        @if (
            empty(config('services.whatsapp.url')) ||
            empty(config('services.whatsapp.key')) ||
            empty(config('services.whatsapp.socket_url'))
        )
            <div class="alert alert-warning">
                ⚠️ <strong>Konfigurasi tidak lengkap:</strong><br>
                Harap pastikan variabel berikut sudah disetel di file <code>.env</code>:
                <ul>
                    @if (empty(config('services.whatsapp.url')))
                        <li><code>WA_BACKEND_URL</code> belum disetel</li>
                    @endif
                    @if (empty(config('services.whatsapp.key')))
                        <li><code>API_SECRET</code> belum disetel</li>
                    @endif
                    @if (empty(config('services.whatsapp.socket_url')))
                        <li><code>WHATSAPP_SOCKET_URL</code> belum disetel</li>
                    @endif
                </ul>
            </div>
        @endif

        <h2 id="qr-title">Login WhatsApp</h2>
        <p  id="qr-description">Scan QR Code untuk menghubungkan akun WhatsApp Anda</p>
        
        <div class="row mb-4" id="qr-section">
            <div class="col-12 col-md-6">
                <div class="qr-box">
                    <div class="qr-placeholder text-center p-4" id="qr-code-container">
                        <!-- QR code akan muncul di sini -->
                    </div>
                    <p>QR Code akan diperbarui setiap 60 detik</p>
                </div>
                <button id="refresh-qr" class="btn btn-success mt-2">
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
            <div class="card-header"  style="display: block">
                <div class="card-title text-center">Connection Status</div>
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

    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const socket = io("{{ config('services.whatsapp.socket_url') }}/?session={{ Auth::user()->username }}");

            const qrContainer = document.getElementById('qr-code-container');
            const statusText = document.getElementById("status-text");
            const lastConnected = document.getElementById("last-connected");
            const qrSection = document.getElementById("qr-section");
            const qrTitle = document.getElementById("qr-title");
            const qrDesc = document.getElementById("qr-description");
            const refreshBtn = document.getElementById("refresh-qr");
            const logoutCard = document.getElementById("whatsapp-logout");

            const initialStatus = @json($status ?? 'disconnected');
            console.log("📦 Initial status dari Laravel:", initialStatus);
            updateUIStatus(initialStatus);

            async function startSession() {
                try {
                    const res = await fetch("{{ route('whatsapp.start') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await res.json();
                    console.log("🚀 Start session response:", data);
                } catch (error) {
                    console.error("❌ Gagal memulai session:", error);
                }
            }

            function updateUIStatus(status) {
                if (status === "connected") {
                    statusText.textContent = "Connected";
                    statusText.style.color = "green";
                    lastConnected.textContent = 'Tersambung pada: ' + new Date().toLocaleString('id-ID');
                    qrSection.style.display = "none";
                    refreshBtn.style.display = "none";
                    logoutCard.style.display = "block";
                    qrTitle.textContent = "Device Connected";
                    qrDesc.textContent = "Anda sudah terhubung dengan WhatsApp.";
                } else {
                    statusText.textContent = "Not Connected";
                    statusText.style.color = "red";
                    lastConnected.textContent = "";
                    qrSection.style.display = "flex";
                    refreshBtn.style.display = "inline-block";
                    logoutCard.style.display = "none";
                    qrTitle.textContent = "Login WhatsApp";
                    qrDesc.textContent = "Scan QR Code untuk menghubungkan akun WhatsApp Anda.";
                }
            }

            // WebSocket Events
            socket.on("connect", () => {
                console.log("✅ Terhubung ke socket server");
            });

            socket.on("session:qr", ({ session, qr }) => {
                console.log("📷 Menerima QR:", session);
                qrContainer.innerHTML = `<img src="${qr}" width="300" />`;
                updateUIStatus('qr');
            });

            socket.on("session:update", ({ session, status }) => {
                console.log("🔄 Status update:", session, status);
                updateUIStatus(status);
            });

            socket.on("disconnect", () => {
                console.warn("❌ Terputus dari socket server");
                updateUIStatus("disconnected");
            });

            // Tombol QR refresh manual
            refreshBtn.addEventListener('click', startSession);

            // Mulai sesi saat pertama load
            startSession();
        });
    </script>
@endsection