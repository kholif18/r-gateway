@extends('layouts.auth')

@section('title', 'Verifikasi OTP')

@section('content')
    <div class="auth-form">
        <div class="auth-header">
            <h2>Verifikasi OTP</h2>
        </div>

        <form method="POST" action="{{ route('password.otp.verify') }}">
            @csrf

            @if(session('status'))
                <div class="auth-footer mb-4 text-sm text-green-600 text-center">
                    {{ session('status') }} <br>
                    Berlaku selama 3 menit.
                </div>
            @endif

            
            <div class="form-group">
                <label for="otp">Masukkan Kode OTP</label>
                <input type="text" name="otp" id="otp" class="form-control" required>
                @error('otp')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn-auth">Verifikasi</button>
        </form>
        <!-- Countdown Area -->
        
        <div class="auth-footer">
            <p id="countdown" class="text-center text-sm text-gray-600 mt-3">
                Kode OTP berlaku selama <span id="timer">03:00</span>
            </p>
            <input type="hidden" id="expiresAt" value="{{ $expiresAt }}">
            <p class="mt-2" id="resend-link" style="display: none;">
                Belum menerima kode? <a href="{{ route('password.otp.resend') }}" onclick="event.preventDefault(); document.getElementById('resend-form').submit();" class="text-blue-600 hover:underline">Kirim Ulang</a>
            </p>
        </div>
        <form id="resend-form" action="{{ route('password.otp.resend') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const display = document.getElementById('timer');
            const resendLink = document.getElementById('resend-link');
            const expiresAtInput = document.getElementById('expiresAt');
            
            const expiresAt = parseInt(expiresAtInput.value) * 1000;
            const now = new Date().getTime();
            let duration = Math.floor((expiresAt - now) / 1000);

            if (duration <= 0) {
                display.textContent = "00:00";
                resendLink.style.display = 'inline';
                return;
            }

            const timer = setInterval(function () {
                let minutes = Math.floor(duration / 60);
                let seconds = duration % 60;

                display.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

                if (--duration < 0) {
                    clearInterval(timer);
                    display.textContent = "00:00";
                    resendLink.style.display = 'inline';
                }
            }, 1000);
        });

    </script>
@endpush