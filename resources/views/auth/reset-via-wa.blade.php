@extends('layouts.auth')

@section('title', 'Kode OTP')

@section('content')
    <div class="auth-form">
        <div class="auth-header">
            <h2>Kode OTP</h2>
        </div>
        
        <form method="POST" action="{{ route('password.wa-reset') }}">
        @csrf
            <input class="form-control" type="hidden" name="phone" value="{{ session('phone') }}">

            <div class="auth-footer mb-4">
                Kode OTP telah dikirim ke WhatsApp: <strong>{{ session('phone') }}</strong><br>
                Berlaku selama 3 menit.
            </div>

            <div class="mb-2">
                <label for="otp">Kode OTP</label>
                <input class="form-control" id="otp" type="text" name="otp" placeholder="Kode OTP" required>
                @error('otp')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <button type="submit" class="btn-auth">Verifikasi OTP</button>
            </div>
            <div class="auth-footer">
                <p class="mt-2">
                    Belum menerima kode? <a href="#" class="text-blue-600 hover:underline">Kirim Ulang</a>
                </p>
            {{-- {{ route('password.otp.resend', ['phone' => session('phone')]) }} --}}
            </div>
        </form>
    </div>
@endsection
