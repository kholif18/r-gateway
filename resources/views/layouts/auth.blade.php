<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>R Gateway | @yield('title', 'Login')</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
        @stack('head')
    </head>
    <body>
        <div class="auth-container">
            <div class="auth-left">
                <img src="{{ asset('assets/img/logo-white.png') }}" alt="WA-Gateway">
                <h1>R Gateway</h1>
                <p>Platform pengiriman pesan WhatsApp untuk bisnis Anda. Kelola aplikasi Anda dengan pengiriman pesan otomatis dan mudah.</p>
            </div>

            <div class="auth-right">
                @yield('content')
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
