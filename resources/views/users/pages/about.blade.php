@extends('users.layouts.app')

@section('title', 'About Us - Hotelier')

@section('content')

@include('users.components.page-header', ['title' => 'About Us', 'breadcrumb' => 'About'])

<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <h6 class="section-title text-start text-primary text-uppercase">About Us</h6>
                <h1 class="mb-4">Welcome to <span class="text-primary text-uppercase">Hotelier</span></h1>
                <p class="mb-4">{{ $about->intro ?? 'Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet' }}</p>
                <p class="mb-4">{{ $about->body ?? 'Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet' }}</p>
                <div class="row gy-2 gx-4 mb-4">
                    @foreach($features ?? ['All Rooms Air Conditioned','Morning Breakfast','Food Court','Sports Facility','Airport Transfer','Spa & Fitness','Bar & Restaurant','Swimming Pool'] as $feature)
                    <div class="col-sm-6">
                        <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>{{ $feature }}</p>
                    </div>
                    @endforeach
                </div>
                {{-- SINKRONISASI: Mengubah route('contact') menjadi # untuk mencegah error Route Not Found --}}
                <a class="btn btn-primary py-3 px-5 mt-2" href="#">Contact Us</a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.1s"
                             src="{{ asset('img/about-1.jpg') }}" style="margin-top: 25%;" alt="">
                    </div>
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.3s"
                             src="{{ asset('img/about-2.jpg') }}" alt="">
                    </div>
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-50 wow zoomIn" data-wow-delay="0.5s"
                             src="{{ asset('img/about-4.jpg') }}" alt="">
                    </div>
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.7s"
                             src="{{ asset('img/about-3.jpg') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid overflow-hidden px-lg-0">
    <div class="container facts px-lg-0">
        <div class="row g-0 mx-lg-0">
            <div class="col-lg-6 facts-text wow fadeIn" data-wow-delay="0.1s">
                <div class="h-100 px-4 ps-lg-0">
                    <h6 class="section-title text-start text-white text-uppercase mb-3">Why Choose Us</h6>
                    <h1 class="text-white mb-4">We Make Your Stay More Comfortable And Enjoyable</h1>
                    <p class="text-white mb-3 pb-3">
                        Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos.
                        Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet.
                    </p>
                    <a href="{{ route('rooms.index') }}" class="btn btn-primary py-3 px-5">Book A Room</a>
                </div>
            </div>
            <div class="col-lg-6 facts-counter wow fadeIn" data-wow-delay="0.5s">
                <div class="h-100 px-4 pe-lg-0">
                    <div class="row g-5">
                        @foreach($stats ?? [
                            ['icon' => 'fa-hotel', 'count' => 1234, 'label' => 'Hotel Rooms'],
                            ['icon' => 'fa-users', 'count' => 1234, 'label' => 'Happy Clients'],
                            ['icon' => 'fa-user-check', 'count' => 1234, 'label' => 'Staff Members'],
                            ['icon' => 'fa-award', 'count' => 1234, 'label' => 'Awards & Prizes'],
                        ] as $stat)
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start">
                                <i class="fa {{ $stat['icon'] }} fa-2x text-primary me-4 mt-2"></i>
                                <div>
                                    <h2 class="text-white mb-2 counter">{{ $stat['count'] }}</h2>
                                    <p class="text-primary mb-0">{{ $stat['label'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Team</h6>
            <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Luxury</span> Staff</h1>
        </div>
        <div class="row g-4">
            @foreach($teamMembers ?? [
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-1.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-2.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-3.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-4.jpg'],
            ] as $member)
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="rounded shadow overflow-hidden">
                    <div class="position-relative">
                        <img class="img-fluid" src="{{ asset(is_array($member) ? $member['img'] : $member->photo) }}" alt="">
                        <div class="position-absolute start-50 top-100 translate-middle d-flex align-items-center">
                            <a class="btn btn-square btn-primary mx-1" href="#"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href="#"><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="text-center p-4 mt-3">
                        <h5 class="fw-bold mb-0">{{ is_array($member) ? $member['name'] : $member->name }}</h5>
                        <small>{{ is_array($member) ? $member['role'] : $member->position }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection