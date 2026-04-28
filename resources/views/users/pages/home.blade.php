@extends('users.layouts.app')

@section('title', 'Beranda')

@section('content')

{{-- ═══════════════════════════════════════
     PROMO BANNER (MUNCUL JIKA ADA PROMO)
═══════════════════════════════════════ --}}
@if(isset($activeDiscounts) && $activeDiscounts->count() > 0)
<div class="container-fluid bg-warning text-dark py-2 px-0 text-center fw-bold shadow-sm" style="z-index: 99; position: relative;">
    <marquee behavior="scroll" direction="left" scrollamount="6">
        @foreach($activeDiscounts as $promo)
            🎉 <strong>PROMO SPESIAL: {{ $promo->name }}!</strong> Dapatkan potongan 
            {{ $promo->discount_type == 'percentage' ? $promo->discount_value.'%' : 'Rp '.number_format($promo->discount_value, 0, ',', '.') }} 
            (Berlaku untuk: {{ str_replace('_', ' ', $promo->applicable_to) }} hingga {{ \Carbon\Carbon::parse($promo->valid_until)->format('d M Y') }}). &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        @endforeach
    </marquee>
</div>
@endif

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
                            <a href="{{ route('rooms.index') }}" class="btn btn-primary py-3 px-5 me-3 animated slideInRight">
                                Book A Room
                            </a>
                            @if(!session()->has('guest_id'))
                                <a href="{{ route('guest.login') }}" class="btn btn-outline-light py-3 px-5 animated slideInRight">
                                    Login / Register
                                </a>
                            @endif
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
            <form action="{{ route('rooms.index') }}" method="GET">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="date" name="check_in" class="form-control" placeholder="Check In" value="{{ request('check_in') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="check_out" class="form-control" placeholder="Check Out" value="{{ request('check_out') }}">
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
                <h6 class="section-title text-start text-primary text-uppercase">Tentang Kami</h6>
                <h1 class="mb-4">Selamat Datang di <span class="text-primary text-uppercase">Hotel Neo</span></h1>
                <p class="mb-4">Nikmati kenyamanan paripurna dan layanan bintang lima di Hotel Neo. Pilihan tepat untuk staycation maupun perjalanan bisnis Anda dengan fasilitas berkelas dunia.</p>
                <div class="row gy-2 gx-4 mb-4">
                    @foreach(['AC di Setiap Kamar', 'Sarapan Pagi', 'Restoran Bintang 5', 'Pusat Kebugaran', 'Layanan Antar Bandara', 'Spa Terapi', 'Wi-Fi Kecepatan Tinggi', 'Kolam Renang'] as $feature)
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
     OUR ROOMS (MENGGUNAKAN ROOMTYPE)
═══════════════════════════════════════ --}}
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Kamar Kami</h6>
            <h1 class="mb-5">Eksplorasi Kamar <span class="text-primary text-uppercase">Mewah</span> Kami</h1>
        </div>
        <div class="row g-4">
            @forelse($featuredRooms as $type)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="room-item shadow rounded overflow-hidden">
                    <div class="position-relative">
                        <img class="img-fluid w-100" 
                             src="{{ $type->foto ? asset('storage/' . $type->foto) : asset('img/room-1.jpg') }}" 
                             alt="{{ $type->name }}" style="height: 250px; object-fit: cover;">
                        <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">
                            Rp {{ number_format($type->price, 0, ',', '.') }}/Malam
                        </small>
                    </div>
                    <div class="p-4 mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ $type->name }}</h5>
                            <div class="ps-2">
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <small class="border-end me-3 pe-3"><i class="fa fa-bed text-primary me-2"></i>{{ $type->bed_type }}</small>
                            <small class="border-end me-3 pe-3"><i class="fa fa-bath text-primary me-2"></i>{{ $type->bath_count }} Bath</small>
                            <small><i class="fa fa-user text-primary me-2"></i>{{ $type->adult_capacity }} Dewasa</small>
                        </div>
                        <p class="text-body mb-3">{{ Str::limit($type->description, 80) }}</p>
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-sm btn-primary rounded py-2 px-4" href="{{ route('rooms.show', $type->id) }}">Detail Kamar</a>
                            <a class="btn btn-sm btn-dark rounded py-2 px-4" href="{{ route('checkout.room', ['room_type_id' => $type->id]) }}">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">Belum ada tipe kamar yang tersedia.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     RESTAURANT & MENUS
═══════════════════════════════════════ --}}
<div class="container-fluid py-5 bg-dark text-light" style="margin-top: 50px; margin-bottom: 50px;">
    <div class="container py-5">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Restoran Kami</h6>
            <h1 class="mb-5 text-white">Cicipi Hidangan <span class="text-primary text-uppercase">Bintang Lima</span></h1>
        </div>
        <div class="row g-4">
            @forelse($featuredMenus as $menu)
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="d-flex align-items-center bg-white rounded p-3 shadow-sm">
                    <img class="flex-shrink-0 rounded" src="{{ $menu->foto_url ? asset('storage/' . $menu->foto_url) : asset('img/menu-1.jpg') }}" alt="{{ $menu->name }}" style="width: 100px; height: 100px; object-fit: cover;">
                    <div class="w-100 d-flex flex-column text-start ps-4">
                        <h5 class="d-flex justify-content-between border-bottom pb-2 text-dark">
                            <span>{{ $menu->name }}</span>
                            <span class="text-primary">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                        </h5>
                        <small class="text-muted">{{ Str::limit($menu->description, 60) }}</small>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-white">Menu belum tersedia.</div>
            @endforelse
            <div class="col-12 text-center mt-5 wow fadeInUp" data-wow-delay="0.3s">
                <a href="{{ route('menus') }}" class="btn btn-primary py-3 px-5">Lihat Semua Menu</a>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     PACKAGES / BUNDLING
═══════════════════════════════════════ --}}
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Paket Penawaran</h6>
            <h1 class="mb-5">Paket <span class="text-primary text-uppercase">Spesial</span> Untuk Anda</h1>
        </div>
        <div class="row g-4">
            @forelse($packages as $pkg)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="rounded shadow overflow-hidden border border-light">
                    <div class="p-4 text-center bg-light">
                        <h4 class="fw-bold mb-3">{{ $pkg->name }}</h4>
                        <h2 class="text-primary mb-0">Rp {{ number_format($pkg->total_price, 0, ',', '.') }}</h2>
                    </div>
                    <div class="p-4 bg-white">
                        <p class="text-muted text-center mb-4">{{ $pkg->description }}</p>
                        @if($pkg->roomType)
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span>Termasuk Kamar:</span>
                                <span class="fw-bold">{{ $pkg->roomType->name }}</span>
                            </div>
                        @endif
                        <a class="btn btn-primary w-100 mt-3" href="{{ route('package.customize', $pkg->id) }}">Beli Paket Ini</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">Belum ada paket bundling khusus saat ini.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     OUR TEAM
═══════════════════════════════════════ --}}
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Tim Kami</h6>
            <h1 class="mb-5">Bertemu Dengan <span class="text-primary text-uppercase">Staf Ahli</span> Kami</h1>
        </div>
        <div class="row g-4">
            @forelse($staffs as $staff)
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="rounded shadow overflow-hidden">
                    <div class="position-relative text-center bg-light p-4">
                        <img class="img-fluid rounded-circle mb-3" src="{{ $staff->foto ? asset('storage/' . $staff->foto) : asset('img/team-1.jpg') }}" alt="{{ $staff->name }}" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <div class="text-center p-4 mt-1 bg-white">
                        <h5 class="fw-bold mb-0">{{ $staff->name }}</h5>
                        <small class="text-primary text-uppercase fw-bold">{{ $staff->role->name ?? 'Staf Hotel' }}</small>
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