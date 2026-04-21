@extends('users.layouts.app')

@section('title', 'Hotel Neo - Hotel & Resort | Home')

@section('content')

{{-- ═══════════════════════════════════════
     HERO CAROUSEL
═══════════════════════════════════════ --}}
<div class="container-fluid p-0 wow fadeIn" data-wow-delay="0.1s">
    <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($carouselSlides ?? [
                ['img' => 'img/carousel-1.jpg', 'title' => 'Enjoy A Luxury Experience', 'subtitle' => 'Discover A Brand Luxurious Hotel'],
                ['img' => 'img/carousel-2.jpg', 'title' => 'Enjoy A Luxury Experience', 'subtitle' => 'The Best Luxury Hotel For Your Trip'],
            ] as $index => $slide)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img class="w-100" src="{{ asset($slide['img']) }}" alt="{{ $slide['title'] }}">
                <div class="carousel-caption">
                    <div class="container">
                        <div class="col-lg-7 text-start">
                            <p class="fs-4 text-white animated slideInRight">{{ $slide['subtitle'] }}</p>
                            <h1 class="display-1 text-white mb-5 animated slideInRight">{{ $slide['title'] }}</h1>
                            <a href="{{ route('rooms') }}" class="btn btn-primary py-3 px-5 me-3 animated slideInRight">
                                Book A Room
                            </a>
                            @guest
                                <a href="{{ route('guest.login') }}" class="btn btn-outline-light py-3 px-5 animated slideInRight">
                                    Login / Register
                                </a>
                            @endguest
                            @auth
                                <a href="{{ route('rooms') }}" class="btn btn-outline-light py-3 px-5 animated slideInRight">
                                    Explore Rooms
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════
     BOOKING FORM
═══════════════════════════════════════ --}}
<div class="container-fluid booking pb-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="bg-white shadow" style="padding: 35px;">
            <form action="{{ route('rooms') }}" method="GET">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="check_in" class="form-control datetimepicker-input" placeholder="Check In" data-toggle="datetimepicker" value="{{ request('check_in') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="check_out" class="form-control datetimepicker-input" placeholder="Check Out" data-toggle="datetimepicker" value="{{ request('check_out') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="adult" class="form-select">
                            <option value="1" {{ request('adult') == 1 ? 'selected' : '' }}>Adult 1</option>
                            <option value="2" {{ request('adult') == 2 ? 'selected' : '' }}>Adult 2</option>
                            <option value="3" {{ request('adult') == 3 ? 'selected' : '' }}>Adult 3</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="child" class="form-select">
                            <option value="0" {{ request('child') == 0 ? 'selected' : '' }}>Child 0</option>
                            <option value="1" {{ request('child') == 1 ? 'selected' : '' }}>Child 1</option>
                            <option value="2" {{ request('child') == 2 ? 'selected' : '' }}>Child 2</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     ABOUT SECTION
═══════════════════════════════════════ --}}
<div class="container-fluid py-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <h6 class="section-title text-start text-primary text-uppercase">About Us</h6>
                <h1 class="mb-4">Welcome to <span class="text-primary text-uppercase">Hotel Neo</span></h1>
                <p class="mb-4">{{ $aboutText ?? 'Nikmati kenyamanan paripurna dan layanan bintang lima di Hotel Neo. Pilihan tepat untuk staycation maupun perjalanan bisnis Anda.' }}</p>
                <div class="row gy-2 gx-4 mb-4">
                    @foreach(['All Rooms Air Conditioned', 'Morning Breakfast', 'Food Court', 'Sports Facility', 'Airport Transfer', 'Spa & Fitness', 'Bar & Restaurant', 'Swimming Pool'] as $feature)
                    <div class="col-sm-6">
                        <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>{{ $feature }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.1s" src="{{ asset('img/about-1.jpg') }}" style="margin-top: 25%;" alt="About 1">
                    </div>
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.3s" src="{{ asset('img/about-2.jpg') }}" alt="About 2">
                    </div>
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-50 wow zoomIn" data-wow-delay="0.5s" src="{{ asset('img/about-4.jpg') }}" alt="About 4">
                    </div>
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.7s" src="{{ asset('img/about-3.jpg') }}" alt="About 3">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     OUR ROOMS (SINKRON DENGAN DATABASE)
═══════════════════════════════════════ --}}
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Rooms</h6>
            <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Luxurious</span> Rooms</h1>
        </div>
        <div class="row g-4">
            {{-- Mengambil data Room beserta relasi RoomType --}}
            @forelse($rooms as $room)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="room-item shadow rounded overflow-hidden">
                    <div class="position-relative">
                        {{-- Gunakan Null-safe operator (?->) agar kebal dari error --}}
                        <img class="img-fluid w-100" 
                             src="{{ $room->roomType?->foto ? asset($room->roomType->foto) : asset('img/room-1.jpg') }}" 
                             alt="{{ $room->roomType?->name ?? 'Room' }}">
                        <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">
                            Rp {{ number_format($room->roomType?->price ?? 0, 0, ',', '.') }}/Night
                        </small>
                    </div>
                    <div class="p-4 mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ $room->roomType?->name ?? 'Tipe Kamar Dihapus' }} <br><span class="fs-6 text-muted">No. {{ $room->room_number }}</span></h5>
                            <div>
                                @for($i = 1; $i <= ($room->roomType?->rating ?? 0); $i++)
                                    <small class="fa fa-star text-primary"></small>
                                @endfor
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <small class="border-end me-3 pe-3">
                                <i class="fa fa-users text-primary me-2"></i>{{ $room->roomType->adult_capacity }} Adult
                            </small>
                            <small class="border-end me-3 pe-3">
                                <i class="fa fa-baby text-primary me-2"></i>{{ $room->roomType->child_capacity }} Child
                            </small>
                            <small>
                                <i class="fa fa-bed text-primary me-2"></i>{{ $room->roomType->bed_type }}
                            </small>
                        </div>
                        <p class="text-body mb-3">{{ Str::limit($room->roomType?->description ?? 'Deskripsi tidak tersedia karena tipe kamar ini tidak ditemukan.', 90) }}</p>
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-sm btn-primary rounded py-2 px-4" href="{{ route('room.detail', $room->id) }}">View Detail</a>
                            
                            <a class="btn btn-sm btn-dark rounded py-2 px-4" href="{{ route('booking', ['room' => $room->id]) }}">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">Belum ada kamar tersedia.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     OUR TEAM (FILTERED ROLES)
═══════════════════════════════════════ --}}
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Team</h6>
            <h1 class="mb-5">Meet Our <span class="text-primary text-uppercase">Expert</span> Team</h1>
        </div>
        <div class="row g-4">
            @forelse($staffs as $staff)
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="rounded shadow overflow-hidden">
                    <div class="position-relative">
                        <img class="img-fluid w-100" src="{{ $staff->foto ? asset($staff->foto) : asset('img/team-1.jpg') }}" alt="{{ $staff->name }}">
                        <div class="position-absolute start-50 top-100 translate-middle d-flex align-items-center">
                            <a class="btn btn-square btn-primary mx-1" href="#"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="text-center p-4 mt-3">
                        <h5 class="fw-bold mb-0">{{ $staff->name }}</h5>
                        {{-- Mengambil nama role dari tabel roles (contoh: Kepala Pelayan) --}}
                        <small class="text-primary text-uppercase fw-bold">{{ $staff->role->name ?? 'Staff' }}</small>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">Data tim belum tersedia.</div>
            @endforelse
        </div>
    </div>
</div>

@endsection