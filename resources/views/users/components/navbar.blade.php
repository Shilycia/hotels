{{-- Topbar --}}
<div class="container-fluid bg-dark text-light px-5 d-none d-lg-block">
    <div class="row gx-0">
        <div class="col-lg-7 px-5 text-start">
            <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                <small class="fa fa-map-marker-alt text-primary me-2"></small>
                <small>{{ config('hotel.address', 'Jl. Akses UI, Depok, Indonesia') }}</small>
            </div>
            <div class="h-100 d-inline-flex align-items-center py-3">
                <small class="far fa-clock text-primary me-2"></small>
                <small>{{ config('hotel.hours', 'Layanan 24 Jam') }}</small>
            </div>
        </div>
        <div class="col-lg-5 px-5 text-end">
            <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                <small class="fa fa-phone-alt text-primary me-2"></small>
                <small>{{ config('hotel.phone', '+62 812 3456 7890') }}</small>
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
            <i class="fa fa-hotel me-3"></i>Hotel Neo
        </h1>
    </a>

    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-4 py-lg-0">
            <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                Beranda
            </a>
            <a href="{{ route('rooms.index') }}" class="nav-item nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                Kamar
            </a>
            <a href="{{ route('restaurant.index') }}" class="nav-item nav-link {{ request()->routeIs('restaurant.*') ? 'active' : '' }}">
                Restoran
            </a>
            <a href="{{ route('about') }}" class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                Tentang Kami
            </a>
        </div>

        {{-- Logika Autentikasi Tamu --}}
        @if(session()->has('guest_id'))
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle btn btn-primary rounded-0 py-4 px-md-5 d-none d-lg-flex align-items-center text-white" 
                   data-bs-toggle="dropdown" role="button" aria-expanded="false" style="color: white !important;">
                    <i class="fas fa-user-circle fa-lg me-2"></i> {{ session('guest_name') }}
                </a>
                
                {{-- Dropdown Menu untuk Tamu --}}
                <div class="dropdown-menu shadow-sm border-0 m-0 rounded-0">
                    <a href="{{ route('guest.profile') }}" class="dropdown-item py-2">
                        <i class="fas fa-id-card me-2 text-primary"></i> Profil & Pesanan
                    </a>
                    <hr class="dropdown-divider">
                    <form action="{{ route('guest.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
            
            {{-- Tampilan Mobile untuk Tamu Login --}}
            <div class="d-lg-none mt-2 mb-3">
                <a href="{{ route('guest.profile') }}" class="nav-item nav-link text-primary"><i class="fas fa-user-circle me-2"></i> Profil & Pesanan</a>
                <form action="{{ route('guest.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-item nav-link text-danger border-0 bg-transparent text-start w-100"><i class="fas fa-sign-out-alt me-2"></i> Keluar</button>
                </form>
            </div>
        @else
            <a href="{{ route('guest.login') }}" class="btn btn-primary rounded-0 py-4 px-md-5 d-none d-lg-block">
                Masuk / Daftar <i class="fa fa-arrow-right ms-3"></i>
            </a>
            {{-- Tampilan Mobile untuk Sign In --}}
            <a href="{{ route('guest.login') }}" class="btn btn-primary w-100 d-block d-lg-none mt-2 mb-3">
                Masuk / Daftar
            </a>
        @endif
    </div>
</nav>