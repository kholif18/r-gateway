@extends('components.app')

@section('title', 'Send Message')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">Test Message</div>
        </div>
        <div class="card-body">
            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ url('/send') }}" method="POST">
            @csrf
                <div class="form-container">
                    <div class="form-section">
                        <div class="form-group">
                            <label for="number">Nomor WhatsApp</label>
                            <div class="search-box">
                                <i class="fas fa-phone"></i>
                                <input type="text" id="number" name="number" class="form-control" placeholder="6281234567890" required>
                            </div>
                        </div>
                        <div class="form-group text-muted">
                            <label for="key">Secret</label>
                            <div class="search-box">
                                <i class="fas fa-key"></i>
                                <input type="text" id="key" name="key" class="form-control" value="sadkjhlaksdhlsakg" readonly>
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
                </div>
            </form>
        </div>
    </div>
@endsection