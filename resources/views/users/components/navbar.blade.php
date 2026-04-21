{{-- Topbar --}}
<div class="container-fluid bg-dark text-light px-5 d-none d-lg-block">
    <div class="row gx-0">
        <div class="col-lg-7 px-5 text-start">
            <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                <small class="fa fa-map-marker-alt text-primary me-2"></small>
                <small>{{ config('hotel.address', '123 Street, Bogor, West Java') }}</small>
            </div>
            <div class="h-100 d-inline-flex align-items-center py-3">
                <small class="far fa-clock text-primary me-2"></small>
                <small>{{ config('hotel.hours', 'Mon - Fri : 09.00 AM - 09.00 PM') }}</small>
            </div>
        </div>
        <div class="col-lg-5 px-5 text-end">
            <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                <small class="fa fa-phone-alt text-primary me-2"></small>
                <small>{{ config('hotel.phone', '+012 345 6789') }}</small>
            </div>
            <div class="h-100 d-inline-flex align-items-center">
                <a class="btn btn-sm-square bg-white text-primary me-1" href="{{ config('hotel.facebook', '#') }}" target="_blank" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a class="btn btn-sm-square bg-white text-primary me-1" href="{{ config('hotel.twitter', '#') }}" target="_blank" aria-label="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a class="btn btn-sm-square bg-white text-primary me-1" href="{{ config('hotel.linkedin', '#') }}" target="_blank" aria-label="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a class="btn btn-sm-square bg-white text-primary me-0" href="{{ config('hotel.instagram', '#') }}" target="_blank" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0 px-4 px-lg-5">
    <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center">
        <h1 class="m-0 text-primary">
            <i class="fa fa-hotel me-3"></i>{{ config('hotel.name', 'Hotelier') }}
        </h1>
    </a>

    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-4 py-lg-0">
            <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                Home
            </a>
            <a href="{{ route('about') }}" class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                About
            </a>
            <a href="{{ route('services') }}" class="nav-item nav-link {{ request()->routeIs('services') ? 'active' : '' }}">
                Services
            </a>
            <a href="{{ route('rooms') }}" class="nav-item nav-link {{ request()->routeIs('rooms') ? 'active' : '' }}">
                Rooms
            </a>
            <a href="{{ route('menus') }}" class="nav-item nav-link {{ request()->routeIs('menus') ? 'active' : '' }}">
                Menus
            </a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('team', 'testimonial') ? 'active' : '' }}"
                    data-bs-toggle="dropdown" role="button" aria-expanded="false">
                    Pages
                </a>
                <div class="dropdown-menu shadow-sm border-0 m-0">
                    <a href="{{ route('team') }}" class="dropdown-item {{ request()->routeIs('team') ? 'active' : '' }}">
                        Our Team
                    </a>
                    <a href="{{ route('testimonial') }}" class="dropdown-item {{ request()->routeIs('testimonial') ? 'active' : '' }}">
                        Testimonial
                    </a>
                </div>
            </div>
            <a href="{{ route('contact') }}" class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                Contact
            </a>
        </div>

        @if(session('guest_id'))
            <a href="{{ route('guest.profile') }}" class="nav-item nav-link">My Profile</a>
        @else
            <a href="{{ route('guest.login') }}" class="btn btn-primary rounded-0 py-4 px-md-5 d-none d-lg-block">Sign In<i class="fa fa-arrow-right ms-3"></i></a>
        @endif
    </div>
</nav>