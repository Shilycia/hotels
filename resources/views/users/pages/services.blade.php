@extends('users.layouts.app')

@section('title', 'Our Services - Hotelier')

@section('content')

@include('users.components.page-header', ['title' => 'Our Services', 'breadcrumb' => 'Services'])

<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Services</h6>
            <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Services</span></h1>
        </div>
        <div class="row g-4">
            @forelse($services ?? [] as $service)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <a class="service-item rounded" href="#">
                    <div class="service-icon bg-transparent border rounded p-1">
                        <div class="w-100 h-100 border rounded d-flex align-items-center justify-content-center">
                            <i class="fa {{ $service->icon }} fa-2x img-fluid text-primary"></i>
                        </div>
                    </div>
                    <h5 class="mb-3">{{ $service->title }}</h5>
                    <p class="text-body mb-0">{{ $service->description }}</p>
                </a>
            </div>
            @empty
            @foreach([
                ['icon' => 'fa-hotel', 'title' => 'Rooms & Appartment', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
                ['icon' => 'fa-utensils', 'title' => 'Food & Restaurant', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
                ['icon' => 'fa-spa', 'title' => 'Spa & Fitness', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
                ['icon' => 'fa-swimmer', 'title' => 'Sports & Gaming', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
                ['icon' => 'fa-glass-cheers', 'title' => 'Event & Party', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
                ['icon' => 'fa-dumbbell', 'title' => 'GYM & Yoga', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
            ] as $service)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <a class="service-item rounded" href="#">
                    <div class="service-icon bg-transparent border rounded p-1">
                        <div class="w-100 h-100 border rounded d-flex align-items-center justify-content-center">
                            <i class="fa {{ $service['icon'] }} fa-2x img-fluid text-primary"></i>
                        </div>
                    </div>
                    <h5 class="mb-3">{{ $service['title'] }}</h5>
                    <p class="text-body mb-0">{{ $service['desc'] }}</p>
                </a>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</div>

@endsection