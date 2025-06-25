<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WA Gateway | Reset Password </title>
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
                        <h2>Reset Password</h2>
                        <p>Masukkan OTP dari whatsapp anda dan buat password baru.</p>
                    </div>
                    
                    <form method="POST" action="{{ route('password.wa-reset') }}">
                    @csrf
                        <div class="mb-2">
                            <input class="form-control" type="text" name="phone" placeholder="Nomor WhatsApp" required>
                        </div>

                        <div class="mb-2">
                            <input class="form-control" type="text" name="otp" placeholder="Kode OTP" required>
                        </div>
                        
                        <div class="mb-2">
                            <label for="password">New Password</label>
                            <div class="password-container">
                                <input id="password" class="form-control" type="password" name="password" placeholder="********" required autocomplete="new-password">
                                <span class="password-toggle" id="toggle-password-1">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-2">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="password-container">
                                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" placeholder="********" required autocomplete="new-password">
                                <span class="password-toggle" id="toggle-password-2">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('password_confirmation')
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