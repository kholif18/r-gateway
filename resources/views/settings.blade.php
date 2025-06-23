@extends('components.app')

@section('title', 'Settings')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">Konfigurasi Sistem</div>
        </div>
        <div class="card-body">
            <div class="settings-container">
                <!-- API Token Settings -->
                <div class="settings-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <div class="section-title">Token API</div>
                            <div class="section-description">Kelola token API untuk integrasi dengan sistem eksternal</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="api-token">Token API</label>
                        <div class="token-display">
                            <input type="text" id="api-token" name="api_token" class="form-control" value="{{ $settings['api_token'] }}">
                            <button class="token-action" id="copy-token" title="Salin token">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button class="token-action" id="refresh-token" title="Buat token baru">
                                <i class="fas fa-sync"></i>
                            </button>
                        </div>
                        <div class="form-hint">Token ini digunakan untuk otentikasi saat mengakses API WA-Gateway</div>
                    </div>
                    
                    <div class="toggle-container">
                        <label class="toggle-label">Aktifkan API Access</label>
                        <label class="switch">
                            <input type="checkbox" name="api_access" {{ $settings['api_access'] ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                
                <!-- Sender Settings -->
                <div class="settings-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <div class="section-title">Nomor Pengirim Default</div>
                            <div class="section-description">Atur nomor WhatsApp yang akan digunakan sebagai pengirim pesan</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="default-sender">Nomor Pengirim</label>
                        <input type="text" id="default-sender" class="form-control" value="+6281122334455" placeholder="Contoh: +628123456789">
                        <div class="form-hint">Format nomor harus menggunakan kode negara (mis. +62 untuk Indonesia)</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="sender-name">Nama Pengirim</label>
                        <input type="text" id="sender-name" class="form-control" value="Customer Support" placeholder="Nama yang akan ditampilkan">
                    </div>
                    
                    <div class="toggle-container">
                        <label class="toggle-label">Gunakan nama pengirim</label>
                        <label class="switch">
                            <input type="checkbox" id="use-sender-name" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                
                <!-- Advanced Settings -->
                <div class="settings-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div>
                            <div class="section-title">Pengaturan Lanjutan</div>
                            <div class="section-description">Konfigurasi timeout, retry, dan parameter teknis lainnya</div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="timeout">Timeout Pengiriman (detik)</label>
                            <input type="number" id="timeout" class="form-control" value="30" min="5" max="120">
                            <div class="form-hint">Waktu maksimal menunggu respon saat mengirim pesan</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="max-retry">Maksimal Percobaan Ulang</label>
                            <input type="number" id="max-retry" class="form-control" value="3" min="0" max="10">
                            <div class="form-hint">Jumlah percobaan ulang jika pengiriman gagal</div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="retry-interval">Interval Percobaan Ulang (detik)</label>
                            <input type="number" id="retry-interval" class="form-control" value="10" min="5" max="60">
                            <div class="form-hint">Waktu tunggu antar percobaan ulang</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="max-queue">Maksimal Pesan dalam Antrian</label>
                            <input type="number" id="max-queue" class="form-control" value="100" min="10" max="1000">
                            <div class="form-hint">Jumlah maksimal pesan yang dapat diantrikan</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="log-level">Level Logging</label>
                        <select id="log-level" class="form-control">
                            <option value="debug">Debug (semua log)</option>
                            <option value="info" selected>Info (rekomendasi)</option>
                            <option value="warning">Warning</option>
                            <option value="error">Error saja</option>
                        </select>
                        <div class="form-hint">Tingkat detail log yang dicatat sistem</div>
                    </div>
                    
                    <div class="toggle-container">
                        <label class="toggle-label">Aktifkan Mode Debug</label>
                        <label class="switch">
                            <input type="checkbox" id="debug-mode">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                
                <!-- Save Settings -->
                <div class="settings-section">
                    <button class="btn" id="save-settings">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                    <button class="btn btn-outline" id="reset-settings">
                        <i class="fas fa-undo"></i> Kembalikan ke Default
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Copy API token to clipboard
        document.getElementById('copy-token').addEventListener('click', function() {
            const token = document.getElementById('api-token-value').textContent;
            navigator.clipboard.writeText(token).then(() => {
                const originalIcon = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => {
                    this.innerHTML = originalIcon;
                }, 2000);
            });
        });

        // Generate new API token
        document.getElementById('refresh-token').addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin membuat token baru? Token lama tidak akan berlaku lagi.')) {
                const spinner = '<i class="fas fa-spinner fa-spin"></i>';
                const originalIcon = this.innerHTML;
                this.innerHTML = spinner;
                
                // Simulate token generation
                setTimeout(() => {
                    const newToken = generateToken();
                    document.getElementById('api-token-value').textContent = newToken;
                    this.innerHTML = '<i class="fas fa-sync"></i>';
                    
                    // Show success message
                    alert('Token baru berhasil dibuat! Pastikan untuk memperbarui token di aplikasi yang terintegrasi.');
                }, 1500);
            }
        });

        // Token generator function
        function generateToken() {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.';
            let token = '';
            for (let i = 0; i < 150; i++) {
                token += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return token;
        }

        // Save settings
        document.getElementById('save-settings').addEventListener('click', function() {
            const formData = new FormData(document.querySelector('form')); // pastikan semua input ada dalam <form>
            fetch('/settings/save', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => res.json()).then(data => {
                alert('Pengaturan berhasil disimpan!');
            });
        });

        // Reset settings
        document.getElementById('reset-settings').addEventListener('click', function () {
            if (confirm('Kembalikan ke pengaturan default?')) {
                fetch('/settings/reset', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(res => res.json()).then(data => {
                    location.reload();
                });
            }
        });
    </script>
@endsection