@extends('layouts.auth')

@section('title', 'Registrasi')

@section('content')
    <!-- Form reset password -->
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
@endsection
@push('scripts')
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
@endpush