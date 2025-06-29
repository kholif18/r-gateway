@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="auth-form">
        <div class="auth-header">
            <h2>Lupa Password</h2>
            <p>Masukkan email atau nomor WhatsApp untuk mereset password.</p>
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('password.email') }}">
        @csrf
            <div>
                <label for="email_or_wa">Email / Nomor WA</label>
                <input id="email_or_wa" class="form-control" type="text" name="email_or_wa" placeholder="Email or username" required autofocus autocomplete="username">
                @error('email_or_wa')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="auth-footer mb-4">
                <p>Jika ingin menerima kode OTP via WhatsApp, pastikan nomor tersebut sudah login di sistem kami.</p>
            </div>

            <div>
                <button type="submit" class="btn-auth">Kirim Link / OTP</button>
            </div>
            
            <div class="auth-footer">
                <p>Belum punya akun? <a href="{{ route('register') }}" id="register-link">Buat akun baru</a></p>
            </div>
        </form>
    </div>
@endsection