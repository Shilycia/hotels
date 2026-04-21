<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="{{ config('hotel.meta_keywords', 'hotel, resort, luxury, booking') }}">
    <meta name="description" content="{{ config('hotel.meta_description', 'Hotelier - Luxury Hotel & Resort') }}">
    <title>@yield('title', config('hotel.name', 'Hotelier') . ' - Hotel & Resort')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Sinkin+Sans:wght@700&display=swap" rel="stylesheet">

    {{-- Icon Fonts --}}
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Libraries --}}
    <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet">

    {{-- Bootstrap --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    {{-- Custom Styles --}}
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>

    {{-- Loading Spinner --}}
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    {{-- Navbar --}}
    @include('users.components.navbar')

    {{-- Page Content --}}
    @yield('content')

    {{-- Footer --}}
    @include('users.components.footer')

    {{-- Back to Top --}}
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top" aria-label="Back to top">
        <i class="bi bi-arrow-up"></i>
    </a>

    {{-- Core Scripts --}}
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Library Scripts --}}
    <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('lib/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    {{-- Main Script --}}
    <script src="{{ asset('js/main.js') }}"></script>

    @stack('scripts')
</body>
</html>