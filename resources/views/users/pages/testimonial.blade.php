@extends('layouts.app')

@section('title', 'Testimonials - Hotelier')

@section('content')

@include('components.page-header', ['title' => 'Testimonial', 'breadcrumb' => 'Testimonial'])

<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Clients Say!!!</h6>
            <h1 class="mb-5">Testimonial <span class="text-primary text-uppercase">Review</span></h1>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
            @forelse($testimonials ?? [] as $testimonial)
            <div class="testimonial-item bg-white rounded overflow-hidden">
                <div class="d-flex align-items-center p-4">
                    <img class="img-fluid flex-shrink-0 rounded"
                         src="{{ asset($testimonial->photo) }}"
                         alt="{{ $testimonial->name }}"
                         style="width: 80px; height: 80px; object-fit: cover;">
                    <div class="ps-4">
                        <h5 class="mb-1">{{ $testimonial->name }}</h5>
                        <small>{{ $testimonial->location }}</small>
                        <div class="mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <small class="fa fa-star {{ $i <= $testimonial->rating ? 'text-primary' : '' }}"></small>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="testimonial-text bg-light text-center p-4">
                    <p class="mb-0">{{ $testimonial->review }}</p>
                </div>
            </div>
            @empty
            @foreach([
                ['name' => 'Client Name 1', 'loc' => 'Profession, Country', 'img' => 'img/testimonial-1.jpg', 'review' => 'Tempor stet labore dolor clita stet diam amet ipsum dolor duo ipsum rebum stet dolor amet diam stet. Est stet ea lorem amet est kasd kasd erat eos'],
                ['name' => 'Client Name 2', 'loc' => 'Profession, Country', 'img' => 'img/testimonial-2.jpg', 'review' => 'Tempor stet labore dolor clita stet diam amet ipsum dolor duo ipsum rebum stet dolor amet diam stet. Est stet ea lorem amet est kasd kasd erat eos'],
                ['name' => 'Client Name 3', 'loc' => 'Profession, Country', 'img' => 'img/testimonial-3.jpg', 'review' => 'Tempor stet labore dolor clita stet diam amet ipsum dolor duo ipsum rebum stet dolor amet diam stet. Est stet ea lorem amet est kasd kasd erat eos'],
                ['name' => 'Client Name 4', 'loc' => 'Profession, Country', 'img' => 'img/testimonial-4.jpg', 'review' => 'Tempor stet labore dolor clita stet diam amet ipsum dolor duo ipsum rebum stet dolor amet diam stet. Est stet ea lorem amet est kasd kasd erat eos'],
            ] as $t)
            <div class="testimonial-item bg-white rounded overflow-hidden">
                <div class="d-flex align-items-center p-4">
                    <img class="img-fluid flex-shrink-0 rounded"
                         src="{{ asset($t['img']) }}"
                         alt="{{ $t['name'] }}"
                         style="width: 80px; height: 80px; object-fit: cover;">
                    <div class="ps-4">
                        <h5 class="mb-1">{{ $t['name'] }}</h5>
                        <small>{{ $t['loc'] }}</small>
                        <div class="mt-1">
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                        </div>
                    </div>
                </div>
                <div class="testimonial-text bg-light text-center p-4">
                    <p class="mb-0">{{ $t['review'] }}</p>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</div>

@endsection