@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="profile-card">
    <form id="profile-form" enctype="multipart/form-data">
        @csrf
        {{-- Avatar --}}
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

        {{-- Form --}}
        <div class="profile-content">
            <h3 class="section-title">Personal information</h3>
            <div class="form-grid">
                {{-- Nama --}}
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name ?? auth()->user()->name) }}">
                </div>
                {{-- Username --}}
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="{{ old('username', $user->username ?? auth()->user()->username) }}">
                </div>
                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email ?? auth()->user()->email) }}">
                </div>
                {{-- Phone --}}
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ \App\Helpers\WhatsappHelper::formatPhoneDisplay(old('phone', $user->phone ?? auth()->user()->phone)) }}">
                </div>
                {{-- Address --}}
                <div class="form-group form-full">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" class="form-control" rows="3">{{ old('address', $user->address ?? auth()->user()->address) }}</textarea>
                </div>
            </div>

            {{-- Password --}}
            <h3 class="section-title">Account Settings</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="password-container">
                        <input id="password" class="form-control" type="password" name="password" placeholder="Add new password">
                        <span class="password-toggle" id="toggle-password-1"><i class="fas fa-eye"></i></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Password Confirmation</label>
                    <div class="password-container">
                        <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" placeholder="Retype password">
                        <span class="password-toggle" id="toggle-password-2"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div>
                <button type="button" class="btn btn-success" id="btn-save">
                    <i class="fas fa-save"></i> Save change
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Toast --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
    <div id="feedbackToast" class="toast border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div id="toastHeader" class="toast-header">
            <strong class="me-auto" id="toastTitle"></strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastBody"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('btn-save').addEventListener('click', async () => {
        const form = document.getElementById('profile-form');
        const formData = new FormData(form);
        formData.append('_method', 'PUT');

        try {
            const response = await fetch("{{ route('profile.update', auth()->user()->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                const text = await response.text();
                console.error("Expected JSON, got:", text.slice(0, 100));
                showToast('error', 'Server error: unexpected response format');
                return;
            }

            const data = await response.json();

            if (response.ok) {
                showToast('success', data.message || 'Profile updated successfully!');
                setTimeout(() => window.location.reload(), 2500); // ⏳ delay agar toast terlihat
            } else {
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0][0];
                    showToast('error', firstError);
                } else {
                    showToast('error', data.message || 'Something went wrong.');
                }
            }

        } catch (err) {
            console.error(err);
            showToast('error', 'Request failed.');
        }
    });

    function showToast(title, message, type = 'success') {
        const toastEl = document.getElementById('feedbackToast');
        const toast = new bootstrap.Toast(toastEl);

        const titleEl = document.getElementById('toastTitle');
        const bodyEl = document.getElementById('toastBody');
        const header = document.getElementById('toastHeader');

        // Reset
        header.className = 'toast-header';
        toastEl.classList.remove('bg-success', 'bg-danger');

        titleEl.innerText = title;
        bodyEl.innerText = message;

        if (type === 'success') {
            header.classList.add('bg-success', 'text-white');
            toastEl.classList.add('bg-success', 'text-white');
        } else {
            header.classList.add('bg-danger', 'text-white');
            toastEl.classList.add('bg-danger', 'text-white');
        }

        toast.show();
    }


    // Avatar Preview
    document.getElementById('avatar')?.addEventListener('change', function(e) {
        const preview = document.getElementById('avatar-preview');
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = event => preview.src = event.target.result;
            reader.readAsDataURL(file);
        }
    });

    // Password Toggle
    function setupPasswordToggle(inputId, toggleId) {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(toggleId);
        if (input && toggle) {
            toggle.addEventListener('click', () => {
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                toggle.innerHTML = visible
                    ? '<i class="fas fa-eye"></i>'
                    : '<i class="fas fa-eye-slash"></i>';
            });
        }
    }

    setupPasswordToggle('password', 'toggle-password-1');
    setupPasswordToggle('password_confirmation', 'toggle-password-2');
</script>
@endpush
