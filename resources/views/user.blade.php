@extends('components.app')

@section('title', 'My Profile')

@section('content')
    <div class="profile-card">
        <div class="profile-header">
            <div class="avatar-wrapper">
                <div class="profile-avatar">
                    <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=500&q=80" id="avatar-preview" alt="Avatar">
                </div>
                <label class="avatar-edit" for="avatar-upload">
                    <i class="fas fa-camera"></i>
                </label>
                <input type="file" id="avatar-upload" name="avatar" accept="image/*" style="display: none;">
            </div>
            <h2>Admin Dashboard</h2>
            <p>Administrator Sistem</p>
        </div>
        
        <div class="profile-content">
            <h3 class="section-title">Informasi Pribadi</h3>
            
            <form>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="fullname">Nama Lengkap</label>
                        <input type="text" id="fullname" class="form-control" value="Admin Dashboard">
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" class="form-control" value="admin.dashboard">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" class="form-control" value="admin@example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Telepon</label>
                        <input type="tel" id="phone" class="form-control" value="+62 812 3456 7890">
                    </div>
                    
                    <div class="form-group form-full">
                        <label for="address">Alamat</label>
                        <textarea id="address" class="form-control" rows="3">Jl. Sudirman No. 123, Jakarta Selatan</textarea>
                    </div>
                </div>
                
                <h3 class="section-title">Pengaturan Akun</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" id="password" class="form-control" placeholder="Masukkan password baru">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm-password">Konfirmasi Password</label>
                        <input type="password" id="confirm-password" class="form-control" placeholder="Konfirmasi password baru">
                    </div>
                </div>
                
                <div>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                    <button type="button" class="btn btn-outline">
                        <i class="fas fa-times"></i>
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('avatar-upload').addEventListener('change', function(e) {
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