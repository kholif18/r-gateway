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
            <div class="mb-4">
                <label for="email_or_wa">Email / Nomor WA</label>
                <input id="email_or_wa" class="form-control" type="text" name="email_or_wa" placeholder="Email or username" required autofocus autocomplete="username">
                @error('login')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>
            
            <div>
                <button type="submit" class="btn-auth">Reset Password</button>
            </div>
            
            <div class="auth-footer">
                <p>Belum punya akun? <a href="{{ route('register') }}" id="register-link">Buat akun baru</a></p>
            </div>
        </form>
    </div>
@endsection