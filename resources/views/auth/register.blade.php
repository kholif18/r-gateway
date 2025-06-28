<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>R Gateway | Register </title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    </head>
    <body>
        <div class="auth-container">
            <div class="auth-left">
                <img src="{{ asset('assets/img/logo-white.png') }}" alt="R-Gateway">
                <h1>R Gateway</h1>
                <p>Platform pengiriman pesan WhatsApp untuk bisnis Anda. Kelola aplikasi Anda dengan pengiriman pesan otomatis dan mudah.</p>
            </div>
            
            <div class="auth-right">
                <div class="auth-form">
                    <div class="auth-header">
                        <h2>Registrasi</h2>
                        <p>Silakan daftar untuk masuk ke dashboard</p>
                    </div>
                    
                    <form method="POST" action="{{ route('register') }}">
                    @csrf
                        <div class="mb-2">
                            <label for="name">Name</label>
                            <input id="name" class="form-control" type="text" name="name" placeholder="Name..." required autofocus>
                        </div>

                        <div class="mb-2">
                            <label for="username">Username</label>
                            <input id="username" class="form-control" type="text" name="username" placeholder="username" required>
                            @error('username')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="email">Email</label>
                            <input id="email" class="form-control" type="email" name="email" placeholder="mail@example.com" required>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="mb-2">
                            <label for="password">Password</label>
                            <div class="password-container">
                                <input id="password" class="form-control" type="password" name="password" placeholder="********" required>
                                <span class="password-toggle" id="toggle-password-1">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="password-container">
                                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" placeholder="********" required>
                                <span class="password-toggle" id="toggle-password-2">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <button type="submit" class="btn-auth">Register</button>
                        </div>
                        
                        <div class="auth-footer">
                            <p>Sudah punya akun? <a href="{{ route('login') }}" id="forgot-password">Login</a></p>
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
                    toggleButton.addEventListener('click', function () {
                        const isPassword = passwordInput.type === 'password';
                        passwordInput.type = isPassword ? 'text' : 'password';
                        toggleButton.innerHTML = isPassword
                            ? '<i class="fas fa-eye-slash"></i>'
                            : '<i class="fas fa-eye"></i>';
                    });
                }
            }

            // Aktifkan toggle untuk kedua input
            setupPasswordToggle('password', 'toggle-password-1');
            setupPasswordToggle('password_confirmation', 'toggle-password-2');
        </script>
    </body>
</html>