@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="qr-container">
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
        // 🌟 Element references
        const qrContainer = document.getElementById('qr-code-container');
        const statusText = document.getElementById("status-text");
        const lastConnected = document.getElementById("last-connected");
        const qrSection = document.getElementById("qr-section");
        const qrTitle = document.getElementById("qr-title");
        const qrDesc = document.getElementById("qr-description");
        const refreshBtn = document.getElementById("refresh-qr");
        const logoutCard = document.getElementById("whatsapp-logout");

        // 🌟 Start WhatsApp session
        async function startSession() {
            const res = await fetch("{{ route('whatsapp.start') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });

            const data = await res.json();
            console.log('Start session:', data);
        }

        // 🌟 Update QR image if status is 'qr'
        async function updateQrImage() {
            try {
                const res = await fetch("{{ route('dashboard.status') }}");
                const data = await res.json();

                if (data.status === 'qr') {
                    const qrRes = await fetch("{{ route('whatsapp.qr-image') }}?t=" + new Date().getTime());
                    if (!qrRes.ok) throw new Error('QR tidak tersedia');

                    const blob = await qrRes.blob();
                    const url = URL.createObjectURL(blob);
                    qrContainer.innerHTML = `<img src="${url}" width="300" />`;
                } else if (data.status === 'connected') {
                    qrContainer.innerHTML = `<p class="text-success">Anda sudah terhubung.</p>`;
                } else {
                    qrContainer.innerHTML = `<p class="text-muted">Sesi belum dimulai.</p>`;
                }
            } catch (err) {
                console.error("Gagal update QR:", err);
                qrContainer.innerHTML = `<p class="text-danger">Gagal memuat QR</p>`;
            }
        }

        // 🌟 Update status UI
        async function updateStatus() {
            try {
                const res = await fetch("{{ route('whatsapp.status') }}");
                const data = await res.json();

                if (data.status === "connected") {
                    statusText.textContent = "Terkoneksi";
                    statusText.style.color = "green";

                    lastConnected.textContent = 'Tersambung pada: ' + new Date().toLocaleString('id-ID', {
                        weekday: 'long', year: 'numeric', month: 'long',
                        day: 'numeric', hour: '2-digit', minute: '2-digit'
                    });

                    qrSection.style.display = "none";
                    refreshBtn.style.display = "none";
                    logoutCard.style.display = "block";
                    qrTitle.textContent = "Perangkat Terkoneksi";
                    qrDesc.textContent = "Anda sudah terhubung dengan WhatsApp.";
                } else {
                    statusText.textContent = "Belum terkoneksi";
                    statusText.style.color = "red";

                    lastConnected.textContent = "";
                    qrSection.style.display = "flex";
                    refreshBtn.style.display = "inline-block";
                    logoutCard.style.display = "none";
                    qrTitle.textContent = "Login WhatsApp";
                    qrDesc.textContent = "Scan QR Code untuk menghubungkan akun WhatsApp Anda.";
                }
            } catch (err) {
                console.error("Gagal cek status:", err);
                statusText.textContent = "Error";
                statusText.style.color = "gray";
            }
        }

        // 🌟 Inisialisasi saat halaman dimuat
        async function init() {
            try {
                await startSession();
                await updateStatus();
                await updateQrImage();
            } catch (err) {
                console.error("Inisialisasi gagal:", err);
            }

            // ⏲️ Cek status dan QR secara berkala
            setInterval(() => {
                updateStatus();
                updateQrImage();
            }, 10000); // 10 detik
        }

        // 🌟 Tombol Refresh QR manual
        refreshBtn.addEventListener('click', () => {
            updateQrImage();
        });

        // 🚀 Jalankan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', init);
</script>

    {{-- <script>
        const qrContainer = document.getElementById('qr-code-container');
        const statusText = document.getElementById('status-text');
        const lastConnected = document.getElementById('last-connected');

        async function startSession() {
            fetch("{{ route('whatsapp.start') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
            const data = await res.json();
            console.log('Start session:', data);
        }

        function loadQrImage() {
            fetch("{{ route('dashboard.status') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'connected') {
                        qrContainer.innerHTML = `<p class="text-success">Anda sudah terhubung.</p>`;
                    } else if (data.status === 'qr') {
                        fetch("{{ route('whatsapp.qr-image') }}?t=" + new Date().getTime())
                            .then(res => {
                                if (!res.ok) throw new Error('QR tidak tersedia');
                                return res.blob();
                            })
                            .then(blob => {
                                const url = URL.createObjectURL(blob);
                                qrContainer.innerHTML = `<img src="${url}" width="300" />`;
                            })
                            .catch(err => {
                                qrContainer.innerHTML = `<p class="text-danger">Gagal memuat QR</p>`;
                            });
                    } else {
                        qrContainer.innerHTML = `<p class="text-muted">Sesi belum dimulai.</p>`;
                    }
                })
                .catch(err => {
                    qrContainer.innerHTML = `<p class="text-danger">Gagal memuat status sesi</p>`;
                });
        }

        // function loadQrImage() {
        //     fetch("{{ route('whatsapp.qr-image') }}?t=" + new Date().getTime())
        //         .then(res => {
        //             if (!res.ok) throw new Error('QR tidak tersedia');
        //             return res.blob();
        //         })
        //         .then(blob => {
        //             const url = URL.createObjectURL(blob);
        //             qrContainer.innerHTML = `<img src="${url}" width="300" />`;
        //         })
        //         .catch(err => {
        //             qrContainer.innerHTML = `<p style="color: red;">QR tidak tersedia</p>`;
        //         });
        // }

        function checkStatus() {
            fetch("{{ route('whatsapp.status') }}")
            .then(res => res.json())
            .then(data => {
                const statusText = document.getElementById("status-text");
                const lastConnected = document.getElementById("last-connected");
                const qrSection = document.getElementById("qr-section");
                const qrTitle = document.getElementById("qr-title");
                const qrDesc = document.getElementById("qr-description");
                const refreshBtn = document.getElementById("refresh-qr");
                const logoutCard = document.getElementById("whatsapp-logout");

                if (data.status === "connected") {
                    statusText.textContent = "Terkoneksi";
                    statusText.style.color = "green";

                    // Tambahkan waktu koneksi
                    lastConnected.textContent = 'Tersambung pada: ' + new Date().toLocaleString('id-ID', {
                        weekday: 'long', year: 'numeric', month: 'long',
                        day: 'numeric', hour: '2-digit', minute: '2-digit'
                    });

                    // Sembunyikan bagian QR dan tombol refresh
                    qrSection.style.display = "none";
                    refreshBtn.style.display = "none";

                    // Tampilkan tombol logout
                    logoutCard.style.display = "block";

                    // Ubah judul dan deskripsi
                    qrTitle.textContent = "Perangkat Terkoneksi";
                    qrDesc.textContent = "Anda sudah terhubung dengan WhatsApp.";
                } else {
                    statusText.textContent = "Belum terkoneksi";
                    statusText.style.color = "red";

                    lastConnected.textContent = ""; // Kosongkan info waktu

                    // Tampilkan kembali QR dan refresh button
                    qrSection.style.display = "flex";
                    refreshBtn.style.display = "inline-block";
                    logoutCard.style.display = "none";

                    // Ubah kembali judul dan deskripsi
                    qrTitle.textContent = "Login WhatsApp";
                    qrDesc.textContent = "Scan QR Code untuk menghubungkan akun WhatsApp Anda.";
                }
            })
            .catch(err => {
                console.error("Gagal cek status:", err);
            });
        }

        document.getElementById('refresh-qr').addEventListener('click', () => {
            loadQrImage();
        });

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                await startSession(); // tunggu selesai
                await loadQrImage();
                await checkStatus();
            } catch (err) {
                console.error("Gagal memulai sesi:", err);
            }

            setInterval(() => {
                loadQrImage();
                checkStatus();
            }, 60000);
        });
    </script> --}}
@endsection