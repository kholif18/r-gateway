@extends('layouts.app')

@section('title', 'Send Message')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">Test Message</div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @elseif (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('whatsapp.message.send') }}">
            @csrf
                <div class="form-container">
                    <div class="form-section">
                        <div class="form-group">
                            <label for="number">WhatsApp Number</label>
                            <div class="search-box">
                                <i class="fas fa-phone"></i>
                                <input type="text" id="number" name="number" class="form-control" placeholder="6281234567890" required>
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
@endsection