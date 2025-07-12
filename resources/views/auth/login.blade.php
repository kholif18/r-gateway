@extends('layouts.auth')

@section('title', 'Login')

@section('content')
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
            <p class="mb-2">Lupa password? <a href="{{ route('password.request') }}" id="forgot-password">Reset password</a></p>
            <p>Belum punya akun? <a href="{{ route('register') }}" id="register-link">Buat akun baru</a></p>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    setupPasswordToggle('password', 'toggle-password');

    function setupPasswordToggle(inputId, toggleId) {
        const passwordInput = document.getElementById(inputId);
        const toggleButton = document.getElementById(toggleId);

        if (passwordInput && toggleButton) {
            toggleButton.addEventListener('click', function () {
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
</script>
@endpush
