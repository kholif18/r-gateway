@extends('components.app')

@section('title', 'Settings')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">Konfigurasi Sistem</div>
        </div>
        <div class="card-body">
            <form id="settings-form">
                <div class="settings-container">
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
                                <input type="number" id="timeout" name="timeout" class="form-control" value="{{ old('timeout', $settings['timeout'] ?? 30) }}" min="5" max="120">
                                <div class="form-hint">Waktu maksimal menunggu respon saat mengirim pesan</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="max-retry">Maksimal Percobaan Ulang</label>
                                <input type="number" id="max-retry" name="max-retry" class="form-control" value="{{ old('max-retry', $settings['max-retry'] ?? 3) }}" min="0" max="10">
                                <div class="form-hint">Jumlah percobaan ulang jika pengiriman gagal</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="retry-interval">Interval Percobaan Ulang (detik)</label>
                                <input type="number" id="retry-interval" name="retry-interval" class="form-control" value="{{ old('retry-interval', $settings['retry-interval'] ?? 10) }}" min="5" max="60">
                                <div class="form-hint">Waktu tunggu antar percobaan ulang</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="max-queue">Maksimal Pesan dalam Antrian</label>
                                <input type="number" id="max-queue" name="max-queue" class="form-control" value="{{ old('max-queue', $settings['max-queue'] ?? 100) }}" min="10" max="1000">
                                <div class="form-hint">Jumlah maksimal pesan yang dapat diantrikan</div>
                            </div>
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
            </form>
        </div>
    </div>

    <script>
        // Copy API token to clipboard
        document.getElementById('copy-token').addEventListener('click', function() {
            const token = document.getElementById('api-token').textContent;
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
                    document.getElementById('api-token').value = newToken;
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

        //Reset Settings
        document.getElementById('reset-settings').addEventListener('click', function (event) {
            event.preventDefault(); // Mencegah form submit
            
            if (confirm('Reset semua pengaturan?')) {
                fetch("{{ route('settings.reset') }}", { // Gunakan named route
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error("Reset gagal: " + response.statusText);
                    return response.json();
                })
                .then(data => {
                    alert('Pengaturan berhasil direset!');
                    location.reload(); // Reload halaman
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                    console.error('Reset error:', error);
                });
            }
        });
    </script>
@endsection