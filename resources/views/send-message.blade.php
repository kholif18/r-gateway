@extends('layouts.app')

@section('title', 'Send Message')

@section('content')
    <div class="card">
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