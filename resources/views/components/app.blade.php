<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WA Gateway | @yield('title') </title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    </head>
    <body>
        <x-sidebar></x-sidebar>

        <div class="main-content">
            <x-header></x-header>

            <div class="content">
                @yield('content')
            </div>
        </div>
        <!-- Bootstrap JS Bundle (wajib untuk modal) -->
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            // Mobile menu toggle
            document.querySelector('.menu-toggle').addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
            });

            // Responsive behavior
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    document.querySelector('.sidebar').classList.remove('active');
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
                const avatarContainer = document.querySelector('.avatar-container');

                avatarContainer.addEventListener('click', function (e) {
                e.stopPropagation(); // Hindari bubbling
                this.classList.toggle('show');
                });

                // Klik di luar akan menutup dropdown
                document.addEventListener('click', function (e) {
                if (!avatarContainer.contains(e.target)) {
                    avatarContainer.classList.remove('show');
                }
                });
            });
        </script>
    </body>
</html>