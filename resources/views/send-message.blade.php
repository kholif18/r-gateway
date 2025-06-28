@extends('components.app')

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
                            <h4>Informasi</h4>
                            <p>Masukkan nomor WhatsApp menggunakan kode negara. Contoh: untuk nomor Indonesia, gunakan awalan <strong>62</strong> (bukan 0) tanpa tanda kurung atau spasi. Contoh: <code>6281234567890</code>.</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection