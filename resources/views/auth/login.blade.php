<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WA Gateway | Login </title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    </head>
    <body>
        <div class="auth-container">
            <div class="auth-left">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI2ZmZiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxwYXRoIGQ9Ik0xNyA0aDNhMiAyIDAgMCAxIDIgMnYxNGEyIDIgMCAwIDEtMiAySDRhMiAyIDAgMCAxLTItMlY2YTIgMiAwIDAgMSAyLTJoNyI+PC9wYXRoPjxwYXRoIGQ9Ik0xNyAxN1Y0YTIgMiAwIDAgMC0yLTJIOGEyIDIgMCAwIDAtMiAydjEzYTIgMiAwIDAgMCAyIDJoN2EyIDIgMCAwIDAgMi0yeiI+PC9wYXRoPjwvc3ZnPg==" alt="WA-Gateway">
                <h1>WA Gateway</h1>
                <p>Platform pengiriman pesan WhatsApp untuk bisnis Anda. Kelola aplikasi Anda dengan pengiriman pesan otomatis dan mudah.</p>
            </div>
            
            <div class="auth-right">
                <div class="auth-form">
                    <div class="auth-header">
                        <h2>Masuk ke Dashboard</h2>
                        <p>Silakan masuk dengan akun Anda</p>
                    </div>
                    
                    <form method="POST" action="{{ route('login') }}">
                    @csrf
                        <div class="mb-4">
                            <label for="login">Email</label>
                            <input id="login" class="form-control" type="text" name="login" placeholder="Email or username" required autofocus autocomplete="username">
                            @error('login')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password">Password</label>
                            <div class="password-container">
                                <input id="password" class="form-control" type="password" name="password" placeholder="********" required autocomplete="current-password">
                                <span class="password-toggle" id="toggle-password">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Remember Me -->
                        <div class="block mt-4">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                            </label>
                        </div>
                        
                        <div>
                            <button type="submit" class="btn-auth">Masuk</button>
                        </div>
                        
                        <div class="auth-footer">
                            <p class="mb-2">Lupa password? <a href="{{ route('password.email') }}" id="forgot-password">Reset password</a></p>
                            <p>Belum punya akun? <a href="{{ route('register') }}" id="register-link">Buat akun baru</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            // Toggle password visibility
            function setupPasswordToggle(inputId, toggleId) {
                const passwordInput = document.getElementById(inputId);
                const toggleButton = document.getElementById(toggleId);
                
                if (passwordInput && toggleButton) {
                    toggleButton.addEventListener('click', function() {
                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            toggleButton.innerHTML = '<i class="fas fa-eye-slash"></i>';
                        } else {
                            passwordInput.type = 'password';
                            toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
                        }
                    });
                }
            }
            // Aktifkan toggle untuk kedua input
            setupPasswordToggle('password', 'toggle-password');
        </script>
    </body>
</html>