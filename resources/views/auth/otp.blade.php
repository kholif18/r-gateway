@extends('layouts.auth')

@section('title', 'Verifikasi OTP')

@section('content')
    <div class="auth-form">
        <div class="auth-header">
            <h2>Verifikasi OTP</h2>
        </div>
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.otp.verify') }}">
            @csrf
            {{-- <input type="hidden" name="phone" value="{{ $phone }}"> --}}

            <div class="auth-footer mb-4">
                Kode OTP telah dikirim ke WhatsApp: <strong>{{ session('phone') }}</strong><br>
                Berlaku selama 3 menit.
            </div>
            
            <div class="form-group">
                <label for="otp">Masukkan Kode OTP</label>
                <input type="text" name="otp" id="otp" class="form-control" required>
                @error('otp')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn-auth">Verifikasi</button>
            <!-- Countdown Area -->
            
            <div class="auth-footer">
                <p id="countdown" class="text-center text-sm text-gray-600 mt-3">
                    Kode OTP berlaku selama <span id="timer">03:00</span>
                </p>
                <p class="mt-2" id="resend-link" style="display: none;">
                    Belum menerima kode? <a href="{{ route('password.otp.resend', ['phone' => session('phone')]) }}" class="text-blue-600 hover:underline">Kirim Ulang</a>
                </p>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let duration = 180; // 3 menit dalam detik
            const display = document.getElementById('timer');
            const resendLink = document.getElementById('resend-link');

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