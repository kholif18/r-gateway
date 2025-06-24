@extends('components.app')

@section('title', 'My Profile')

@section('content')
    <div class="profile-card">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="profile-header">
            <div class="avatar-wrapper">
                <div class="profile-avatar">
                    <img src="{{ $user->avatar_url ?? auth()->user()->avatar_url }}" id="avatar-preview" alt="Avatar">
                </div>
                <label class="avatar-edit" for="avatar">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">
                </label>
            </div>
            <h2>{{ $user->name ?? auth()->user()->name }}</h2>
            <p>{{ $user->username ?? auth()->user()->username }}</p>
        </div>
        
        <div class="profile-content">
            <h3 class="section-title">Informasi Pribadi</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name ?? auth()->user()->name) }}">
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="{{ old('username', $user->username ?? auth()->user()->username) }}">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email ?? auth()->user()->email) }}">
                </div>
                
                <div class="form-group">
                    <label for="phone">Telepon</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone ?? auth()->user()->phone) }}">
                </div>
                
                <div class="form-group form-full">
                    <label for="address">Alamat</label>
                    <textarea name="address" id="address" class="form-control" rows="3">{{ old('address', $user->address ?? auth()->user()->address) }}</textarea>
                </div>
            </div>
            
            <h3 class="section-title">Pengaturan Akun</h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password baru">
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Konfirmasi password baru">
                </div>
            </div>
            
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </div>
        </form>
    </div>

    <script>
        // Preview avatar saat dipilih
        document.getElementById('avatar').addEventListener('change', function(e) {
            const preview = document.getElementById('avatar-preview');
            const file = e.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

    </script>

@endsection