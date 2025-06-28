<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>R Gateway | Login </title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    </head>
    <body>
        <div class="auth-container">
            <div class="auth-left">
                <img src="{{ asset('assets/img/logo-white.png') }}" alt="WA-Gateway">
                <h1>R Gateway</h1>
                <p>Platform pengiriman pesan WhatsApp untuk bisnis Anda. Kelola aplikasi Anda dengan pengiriman pesan otomatis dan mudah.</p>
            </div>
            
            <div class="auth-right">
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
            </div>
        </div>
    </body>
</html>