@extends('layouts.app')

@section('title', 'Send Message')

@section('content')
    {{-- <div class="card">
        <div class="card-header">
            <div class="card-title">Test Message</div>
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('whatsapp.message.send') }}">
            @csrf
                <div class="form-container">
                    <div class="form-section">
                        <div class="form-group">
                            <label for="number">WhatsApp Number</label>
                            <div class="search-box">
                                <i class="fas fa-phone"></i>
                                <input type="text" id="number" name="number" class="form-control" placeholder="081234567890" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Message Content</label>
                            <textarea id="message" name="message" class="form-control" placeholder="Type your message here..."></textarea>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn" id="send-button">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </div>
                    </div>
                    <div class="form-section">
                        <div class="form-group">
                            <strong>Informasi</strong>
                            <ul class="text-sm text-gray-600 mt-2">
                                <li>
                                    Status koneksi WA:
                                    @if($waConnected)
                                        <span class="badge bg-success">Terhubung</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Terhubung</span>
                                    @endif
                                </li>
                                <li>Gunakan untuk menguji pengiriman pesan langsung ke WhatsApp.</li>
                                <li>Nomor bisa diawali <code>0</code> atau <code>62</code>, sistem akan menyesuaikan.</li>
                                <li>Pastikan nomor aktif dan terhubung dengan WhatsApp.</li>
                                <li>Pesan akan dikirim dari akun WhatsApp Anda yang sedang login.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div> --}}

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fab fa-whatsapp"></i> WhatsApp Message Tester
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="text-tab" data-bs-toggle="tab" data-bs-target="#text" type="button" role="tab">
                        <i class="fas fa-comment"></i> Pesan Teks
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="file-url-tab" data-bs-toggle="tab" data-bs-target="#file-url" type="button" role="tab">
                        <i class="fas fa-link"></i> File (URL)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="file-upload-tab" data-bs-toggle="tab" data-bs-target="#file-upload" type="button" role="tab">
                        <i class="fas fa-upload"></i> File (Upload)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="group-tab" data-bs-toggle="tab" data-bs-target="#group" type="button" role="tab">
                        <i class="fas fa-users"></i> Kirim ke Grup
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bulk-tab" data-bs-toggle="tab" data-bs-target="#bulk" type="button" role="tab">
                        <i class="fas fa-users"></i> Kirim ke Banyak Nomor
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="myTabContent">
                <!-- Tab Pesan Teks -->
                <div class="tab-pane fade show active" id="text" role="tabpanel">
                    <form method="POST" action="{{ route('message.send') }}">
                        @csrf
                        <input type="hidden" name="type" value="text">
                        <div class="form-container">
                            <div class="form-section">
                                <div class="form-group">
                                    <label for="number">WhatsApp Number</label>
                                    <div class="search-box">
                                        <i class="fas fa-phone"></i>
                                        <input type="text" id="number" name="number" class="form-control" placeholder="081234567890" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="message">Message Content</label>
                                    <textarea id="message" name="message" class="form-control" placeholder="Type your message here..."></textarea>
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn" id="send-button">
                                        <i class="fas fa-paper-plane"></i> Send Message
                                    </button>
                                </div>
                            </div>
                            <div class="form-section info-section">
                                <strong>Informasi</strong>
                                <div class="connection-status">
                                    <span class="status-indicator {{ $waConnected ? 'status-connected' : 'status-disconnected' }}"></span>
                                    Status koneksi WA:
                                    @if($waConnected)
                                        <span class="badge bg-success">Terhubung</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Terhubung</span>
                                    @endif
                                </div>
                                <ul class="text-sm text-gray-600 mt-2">
                                    <li>Gunakan untuk menguji pengiriman pesan langsung ke WhatsApp.</li>
                                    <li>Nomor bisa diawali <code>0</code> atau <code>62</code>, sistem akan menyesuaikan.</li>
                                    <li>Pastikan nomor aktif dan terhubung dengan WhatsApp.</li>
                                    <li>Pesan akan dikirim dari akun WhatsApp Anda yang sedang login.</li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Tab File URL -->
                <div class="tab-pane fade" id="file-url" role="tabpanel">
                    <form method="POST" action="{{ route('message.send') }}">
                    @csrf
                    <input type="hidden" name="type" value="file-url">
                        <div class="form-container">
                            <div class="form-section">
                                <div class="form-group">
                                    <label for="file-url-number">WhatsApp Number</label>
                                    <div class="search-box">
                                        <i class="fas fa-phone"></i>
                                        <input type="text" id="file-url-number" name="number" class="form-control" placeholder="081234567890" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="file_url">File URL</label>
                                    <input type="url" id="file_url" name="file_url" class="form-control" placeholder="https://example.com/file.jpg" required>
                                </div>
                                <div class="form-group">
                                    <label for="file-url-caption">File Caption</label>
                                    <textarea id="file-url-caption" name="caption" class="form-control" placeholder="Description for the file..."></textarea>
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn">
                                        <i class="fas fa-paper-plane"></i> Send File
                                    </button>
                                </div>
                            </div>
                            <div class="form-section info-section">
                                <strong>Informasi</strong>
                                <div class="connection-status">
                                    <span class="status-indicator {{ $waConnected ? 'status-connected' : 'status-disconnected' }}"></span>
                                    Status koneksi WA:
                                    @if($waConnected)
                                        <span class="badge bg-success">Terhubung</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Terhubung</span>
                                    @endif
                                </div>
                                <ul class="text-sm text-gray-600 mt-2">
                                    <li>Kirim file melalui URL langsung ke WhatsApp.</li>
                                    <li>URL harus mengarah ke file yang valid (JPG, PNG, GIF, pdf, docx, xlsx, dll).</li>
                                    <li>Caption bersifat opsional untuk memberikan deskripsi file.</li>
                                    <li>Pastikan URL dapat diakses secara publik oleh sistem.</li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Tab File Upload -->
                <div class="tab-pane fade" id="file-upload" role="tabpanel">
                    <form method="POST" action="{{ route('message.send') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="file-upload">
                        <div class="form-container">
                            <div class="form-section">
                                <div class="form-group">
                                    <label for="upload-number">WhatsApp Number</label>
                                    <div class="search-box">
                                        <i class="fas fa-phone"></i>
                                        <input type="text" id="upload-number" name="number" class="form-control" placeholder="081234567890" required>
                                    </div>
                                </div>
                                <div class="form-group file-upload" id="file-upload-area">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <p class="upload-text">Click to upload or drag & drop an file</p>
                                    <input type="file" id="file" name="file" accept="*" hidden required>
                                    <button type="button" class="btn btn-outline-primary" id="browse-btn">
                                        <i class="fas fa-folder-open"></i> Browse Files
                                    </button>
                                    {{-- <img id="file-preview" class="file-preview" alt="Preview"> --}}
                                    <div id="file-preview-icon" class="file-preview" style="display: none; font-size: 3rem;"></div>
                                </div>
                                <div class="form-group">
                                    <label for="upload-caption">File Caption</label>
                                    <textarea id="upload-caption" name="caption" class="form-control" placeholder="Description for the file..."></textarea>
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn">
                                        <i class="fas fa-paper-plane"></i> Upload & Send
                                    </button>
                                </div>
                            </div>
                            <div class="form-section info-section">
                                <strong>Informasi</strong>
                                <div class="connection-status">
                                    <span class="status-indicator {{ $waConnected ? 'status-connected' : 'status-disconnected' }}"></span>
Status koneksi WA:
@if($waConnected)
    <span class="badge bg-success">Terhubung</span>
@else
    <span class="badge bg-danger">Tidak Terhubung</span>
@endif

                                </div>
                                <ul class="text-sm text-gray-600 mt-2">
                                    <li>Upload file dari perangkat Anda untuk dikirim ke WhatsApp.</li>
                                    <li>Format yang didukung: JPG, PNG, GIF, docx, xlsx, pdf, dll (maks. 50MB).</li>
                                    <li>File akan diunggah ke server sebelum dikirim.</li>
                                    <li>Caption bersifat opsional untuk memberikan deskripsi file.</li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Tab Kirim ke Grup -->
                <div class="tab-pane fade" id="group" role="tabpanel">
                    <form method="POST" action="{{ route('message.send') }}">
                        @csrf
                        <input type="hidden" name="type" value="group">
                        <div class="form-container">
                            <div class="form-section">
                                <div class="form-group">
                                    <label for="group-name">Nama Group</label>
                                    <div class="search-box">
                                        <i class="fas fa-users"></i>
                                        <input type="text" id="group-name" name="group-name" class="form-control" placeholder="Nama grup WhatsApp" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="group-message">Message Content</label>
                                    <textarea id="group-message" name="message" class="form-control" placeholder="Type your message for the group..."></textarea>
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn">
                                        <i class="fas fa-paper-plane"></i> Send to Group
                                    </button>
                                </div>
                            </div>
                            <div class="form-section info-section">
                                <strong>Informasi</strong>
                                <div class="connection-status">
                                    <span class="status-indicator {{ $waConnected ? 'status-connected' : 'status-disconnected' }}"></span>
Status koneksi WA:
@if($waConnected)
    <span class="badge bg-success">Terhubung</span>
@else
    <span class="badge bg-danger">Tidak Terhubung</span>
@endif

                                </div>
                                <ul class="text-sm text-gray-600 mt-2">
                                    <li>Kirim pesan langsung ke grup WhatsApp.</li>
                                    <li>Sesuaikan dengan nama grup, perhatikan huruf BESAR, kecil, angka dan simbol.</li>
                                    <li>Pastikan bot/admin memiliki akses ke grup tersebut.</li>
                                    <li>Pesan akan dikirim sebagai pengguna yang sedang login.</li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tab Kirim ke Banyak Nomor -->
                <div class="tab-pane fade" id="bulk" role="tabpanel">
                    <form method="POST" action="{{ route('message.send') }}">
                        @csrf
                        <input type="hidden" name="type" value="bulk">
                        <div class="form-container">
                            <div class="form-section">
                                <div class="form-group">
                                    <label for="bulk-numbers">WhatsApp Numbers</label>
                                    <textarea id="bulk-numbers" name="phones" class="form-control" placeholder="Masukkan nomor, pisahkan dengan koma atau baris baru" rows="5" required></textarea>
                                    <small class="form-text text-muted">Contoh: 081234567890, 082345678901, ...</small>
                                </div>
                                <div class="form-group">
                                    <label for="bulk-message">Message Content</label>
                                    <textarea id="bulk-message" name="message" class="form-control" placeholder="Type your message here..." rows="5"></textarea>
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn" id="send-bulk-button">
                                        <i class="fas fa-paper-plane"></i> Send Bulk Message
                                    </button>
                                </div>
                            </div>
                            <div class="form-section info-section">
                                <strong>Informasi</strong>
                                <div class="connection-status">
                                    <span class="status-indicator {{ $waConnected ? 'status-connected' : 'status-disconnected' }}"></span>
                                    Status koneksi WA:
                                    @if($waConnected)
                                        <span class="badge bg-success">Terhubung</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Terhubung</span>
                                    @endif
                                </div>
                                <ul class="text-sm text-gray-600 mt-2">
                                    <li>Kirim pesan yang sama ke banyak nomor sekaligus.</li>
                                    <li>Pisahkan nomor dengan koma atau baris baru.</li>
                                    <li>Nomor bisa diawali <code>0</code> atau <code>62</code>, sistem akan menyesuaikan.</li>
                                    <li>Pastikan nomor aktif dan terhubung dengan WhatsApp.</li>
                                    <li>Pesan akan dikirim dari akun WhatsApp Anda yang sedang login.</li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="feedbackToast" class="toast border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div id="toastHeader" class="toast-header">
                <strong class="me-auto" id="toastTitle"></strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastBody"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Handle file upload preview
        document.getElementById('browse-btn').addEventListener('click', function() {
            document.getElementById('file').click();
        });
        
        document.getElementById('file-upload-area').addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#25D366';
            this.style.backgroundColor = 'rgba(37, 211, 102, 0.1)';
        });
        
        document.getElementById('file-upload-area').addEventListener('dragleave', function() {
            this.style.borderColor = '#ddd';
            this.style.backgroundColor = '#fafafa';
        });
        
        document.getElementById('file-upload-area').addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#ddd';
            this.style.backgroundColor = '#fafafa';
            
            const file = e.dataTransfer.files[0];
            handleFile(file);
        });
        
        document.getElementById('file').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                handleFile(this.files[0]);
            }
        });
        
        function handleFile(file) {
            const maxSize = 50 * 1024 * 1024; // 50MB
            if (file.size > maxSize) {
                alert('Ukuran file maksimal 50MB.');
                return;
            }

            const iconPreview = document.getElementById('file-preview-icon');
            const isImage = file.type.startsWith('image/');
            const ext = file.name.split('.').pop().toLowerCase();

            // Reset preview
            iconPreview.style.display = 'none';
            iconPreview.innerHTML = '';

            if (isImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    iconPreview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width:100px; max-height:100px; border-radius:6px;">`;
                    iconPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                // Tentukan ikon berdasarkan ekstensi file
                const iconClass = getFileIconClass(ext);
                iconPreview.innerHTML = `<i class="${iconClass}" style="color:#25D366;"></i>`;
                iconPreview.style.display = 'block';
            }

            // Ubah tampilan box upload
            document.querySelector('.upload-text').textContent = file.name;
            document.querySelector('.upload-icon').innerHTML = '<i class="fas fa-check-circle" style="color:#25D366"></i>';
        }

        function getFileIconClass(ext) {
            switch (ext) {
                case 'pdf': return 'fas fa-file-pdf fa-3x text-danger';
                case 'doc':
                case 'docx': return 'fas fa-file-word fa-3x text-primary';
                case 'xls':
                case 'xlsx': return 'fas fa-file-excel fa-3x text-success';
                case 'zip':
                case 'rar': return 'fas fa-file-archive fa-3x text-warning';
                case 'ppt':
                case 'pptx': return 'fas fa-file-powerpoint fa-3x text-orange';
                case 'mp3':
                case 'wav': return 'fas fa-file-audio fa-3x text-info';
                case 'mp4':
                case 'avi': return 'fas fa-file-video fa-3x text-info';
                case 'txt': return 'fas fa-file-alt fa-3x text-muted';
                default: return 'fas fa-file fa-3x text-secondary';
            }
        }

        function showToast(title, message, type = 'success') {
            const toastEl = document.getElementById('feedbackToast');
            const toast = new bootstrap.Toast(toastEl);

            const titleEl = document.getElementById('toastTitle');
            const bodyEl = document.getElementById('toastBody');
            const header = document.getElementById('toastHeader');

            // Reset
            header.className = 'toast-header';
            toastEl.classList.remove('bg-success', 'bg-danger');

            titleEl.innerText = title;
            bodyEl.innerText = message;

            if (type === 'success') {
                header.classList.add('bg-success', 'text-white');
                toastEl.classList.add('bg-success', 'text-white');
            } else {
                header.classList.add('bg-danger', 'text-white');
                toastEl.classList.add('bg-danger', 'text-white');
            }

            toast.show();
        }

        // ✅ Flash Message Handler (langsung dari Blade)
        @if (session('success'))
            showToast('Sukses', @json(session('success')), 'success');
        @elseif (session('error'))
            showToast('Gagal', @json(session('error')), 'error');
        @endif
    </script>

@endpush