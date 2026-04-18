@extends('layouts.app')

@section('title', 'Hotelier - Hotel & Resort | Home')

@section('content')

{{-- Carousel / Hero --}}
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
                        <div class="column col-lg-7 text-start">
                            <p class="fs-4 text-white animated slideInRight">{{ $slide['subtitle'] }}</p>
                            <h1 class="display-1 text-white mb-5 animated slideInRight">{{ $slide['title'] }}</h1>
                            <a href="{{ route('booking') }}"
                               class="btn btn-primary py-3 px-5 animated slideInRight">
                                Book A Room
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button"
                data-bs-target="#header-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button"
                data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

{{-- Booking Form --}}
<div class="container-fluid booking pb-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="bg-white shadow" style="padding: 35px;">
            <div class="row g-2">
                <div class="col-md-10">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="date" id="date1" data-target-input="nearest">
                                <input type="text"
                                       class="form-control datetimepicker-input"
                                       placeholder="Check in"
                                       data-target="#date1"
                                       data-toggle="datetimepicker" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="date" id="date2" data-target-input="nearest">
                                <input type="text"
                                       class="form-control datetimepicker-input"
                                       placeholder="Check out"
                                       data-target="#date2"
                                       data-toggle="datetimepicker" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option selected>Adult</option>
                                <option value="1">Adult 1</option>
                                <option value="2">Adult 2</option>
                                <option value="3">Adult 3</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option selected>Child</option>
                                <option value="1">Child 1</option>
                                <option value="2">Child 2</option>
                                <option value="3">Child 3</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- About Section --}}
<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <h6 class="section-title text-start text-primary text-uppercase">About Us</h6>
                <h1 class="mb-4">Welcome to <span class="text-primary text-uppercase">Hotelier</span></h1>
                <p class="mb-4">
                    {{ $aboutText ?? 'Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet' }}
                </p>
                <p class="mb-4">
                    Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit.
                </p>
                <div class="row gy-2 gx-4 mb-4">
                    @foreach([
                        'All Rooms Air Conditioned',
                        'Morning Breakfast',
                        'Food Court',
                        'Sports Facility',
                        'Airport Transfer',
                        'Spa & Fitness',
                        'Bar & Restaurant',
                        'Swimming Pool',
                    ] as $feature)
                    <div class="col-sm-6">
                        <p class="mb-0">
                            <i class="fa fa-arrow-right text-primary me-2"></i>{{ $feature }}
                        </p>
                    </div>
                    @endforeach
                </div>
                <a class="btn btn-primary py-3 px-5 mt-2" href="{{ route('about') }}">Explore More</a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-75 wow zoomIn"
                             data-wow-delay="0.1s"
                             src="{{ asset('img/about-1.jpg') }}"
                             style="margin-top: 25%;"
                             alt="About 1">
                    </div>
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-100 wow zoomIn"
                             data-wow-delay="0.3s"
                             src="{{ asset('img/about-2.jpg') }}"
                             alt="About 2">
                    </div>
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-50 wow zoomIn"
                             data-wow-delay="0.5s"
                             src="{{ asset('img/about-4.jpg') }}"
                             alt="About 4">
                    </div>
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-75 wow zoomIn"
                             data-wow-delay="0.7s"
                             src="{{ asset('img/about-3.jpg') }}"
                             alt="About 3">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Video / Facts --}}
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
                    <a href="" class="btn btn-primary py-3 px-5">Explore More</a>
                </div>
            </div>
            <div class="col-lg-6 facts-counter wow fadeIn" data-wow-delay="0.5s">
                <div class="h-100 px-4 pe-lg-0">
                    <div class="row g-5">
                        @foreach([
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

{{-- Room List --}}
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Rooms</h6>
            <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Luxurious</span> Rooms</h1>
        </div>
        <div class="row g-4">
            @forelse($rooms ?? [] as $room)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="room-item shadow rounded overflow-hidden">
                    <div class="position-relative">
                        <img class="img-fluid" src="{{ asset($room->image) }}" alt="{{ $room->name }}">
                        <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">
                            ${{ number_format($room->price) }}/Night
                        </small>
                    </div>
                    <div class="p-4 mt-2">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0">{{ $room->name }}</h5>
                            <div class="ps-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <small class="fa fa-star {{ $i <= $room->rating ? 'text-primary' : '' }}"></small>
                                @endfor
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <small class="border-end me-3 pe-3">
                                <i class="fa fa-bed text-primary me-2"></i>{{ $room->bed_type }}
                            </small>
                            <small class="border-end me-3 pe-3">
                                <i class="fa fa-bath text-primary me-2"></i>{{ $room->bath_count }} Bath
                            </small>
                            <small>
                                <i class="fa fa-wifi text-primary me-2"></i>Wifi
                            </small>
                        </div>
                        <p class="text-body mb-3">{{ Str::limit($room->description, 100) }}</p>
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-sm btn-primary rounded py-2 px-4"
                               href="{{ route('rooms.show', $room->slug) }}">
                                View Detail
                            </a>
                            <a class="btn btn-sm btn-dark rounded py-2 px-4"
                               href="{{ route('booking', ['room' => $room->id]) }}">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            {{-- Fallback placeholder rooms --}}
            @foreach([
                ['name' => 'Junior Suite', 'price' => '100', 'img' => 'img/room-1.jpg'],
                ['name' => 'Executive Suite', 'price' => '150', 'img' => 'img/room-2.jpg'],
                ['name' => 'Super Deluxe', 'price' => '200', 'img' => 'img/room-3.jpg'],
            ] as $room)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="room-item shadow rounded overflow-hidden">
                    <div class="position-relative">
                        <img class="img-fluid" src="{{ asset($room['img']) }}" alt="{{ $room['name'] }}">
                        <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">
                            ${{ $room['price'] }}/Night
                        </small>
                    </div>
                    <div class="p-4 mt-2">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0">{{ $room['name'] }}</h5>
                            <div class="ps-2">
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <small class="border-end me-3 pe-3">
                                <i class="fa fa-bed text-primary me-2"></i>Bed
                            </small>
                            <small class="border-end me-3 pe-3">
                                <i class="fa fa-bath text-primary me-2"></i>2 Bath
                            </small>
                            <small>
                                <i class="fa fa-wifi text-primary me-2"></i>Wifi
                            </small>
                        </div>
                        <p class="text-body mb-3">Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-sm btn-primary rounded py-2 px-4" href="{{ route('rooms') }}">View Detail</a>
                            <a class="btn btn-sm btn-dark rounded py-2 px-4" href="{{ route('booking') }}">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</div>

{{-- Services --}}
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Services</h6>
            <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Services</span></h1>
        </div>
        <div class="row g-4">
            @foreach([
                ['icon' => 'fa-hotel', 'title' => 'Rooms & Appartment', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
                ['icon' => 'fa-utensils', 'title' => 'Food & Restaurant', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
                ['icon' => 'fa-spa', 'title' => 'Spa & Fitness', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
                ['icon' => 'fa-swimmer', 'title' => 'Sports & Gaming', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
                ['icon' => 'fa-glass-cheers', 'title' => 'Event & Party', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
                ['icon' => 'fa-dumbbell', 'title' => 'GYM & Yoga', 'desc' => 'Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.'],
            ] as $service)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <a class="service-item rounded" href="{{ route('services') }}">
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
        </div>
    </div>
</div>

{{-- Video Button (YouTube Popup) --}}
<div class="container-fluid py-5 px-0 wow zoomIn" data-wow-delay="0.1s">
    <div class="row g-0">
        <div class="col-md-6 bg-dark d-flex align-items-center">
            <div class="p-5">
                <h6 class="section-title text-start text-white text-uppercase mb-3">Luxury Living</h6>
                <h1 class="text-white mb-4">Discover A Brand Luxurious Hotel</h1>
                <p class="text-white mb-4">
                    Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos.
                    Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet.
                </p>
                <a href="{{ route('rooms') }}" class="btn btn-primary py-3 px-5">Our Rooms</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="video">
                <button type="button" class="btn btn-play"
                        data-bs-toggle="modal" data-bs-target="#videoModal">
                    <span></span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Video Modal --}}
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">YouTube Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <iframe class="embed-responsive-item" id="video"
                            src="" allowfullscreen allowscriptaccess="always"
                            allow="autoplay"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Team Section --}}
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Team</h6>
            <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Luxury</span> Team</h1>
        </div>
        <div class="row g-4">
            @forelse($teamMembers ?? [] as $member)
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="rounded shadow overflow-hidden">
                    <div class="position-relative">
                        <img class="img-fluid" src="{{ asset($member->photo) }}" alt="{{ $member->name }}">
                        <div class="position-absolute start-50 top-100 translate-middle d-flex align-items-center">
                            <a class="btn btn-square btn-primary mx-1" href="{{ $member->facebook ?? '#' }}">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a class="btn btn-square btn-primary mx-1" href="{{ $member->twitter ?? '#' }}">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a class="btn btn-square btn-primary mx-1" href="{{ $member->instagram ?? '#' }}">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                    <div class="text-center p-4 mt-3">
                        <h5 class="fw-bold mb-0">{{ $member->name }}</h5>
                        <small>{{ $member->position }}</small>
                    </div>
                </div>
            </div>
            @empty
            @foreach([
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-1.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-2.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-3.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-4.jpg'],
            ] as $member)
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="rounded shadow overflow-hidden">
                    <div class="position-relative">
                        <img class="img-fluid" src="{{ asset($member['img']) }}" alt="{{ $member['name'] }}">
                        <div class="position-absolute start-50 top-100 translate-middle d-flex align-items-center">
                            <a class="btn btn-square btn-primary mx-1" href="#"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href="#"><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="text-center p-4 mt-3">
                        <h5 class="fw-bold mb-0">{{ $member['name'] }}</h5>
                        <small>{{ $member['role'] }}</small>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</div>

{{-- Testimonials --}}
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
                ['name' => 'Client Name 1', 'loc' => 'Profession, Country', 'review' => 'Tempor stet labore dolor clita stet diam amet ipsum dolor duo ipsum rebum stet dolor amet diam stet. Est stet ea lorem amet est kasd kasd erat eos'],
                ['name' => 'Client Name 2', 'loc' => 'Profession, Country', 'review' => 'Tempor stet labore dolor clita stet diam amet ipsum dolor duo ipsum rebum stet dolor amet diam stet. Est stet ea lorem amet est kasd kasd erat eos'],
                ['name' => 'Client Name 3', 'loc' => 'Profession, Country', 'review' => 'Tempor stet labore dolor clita stet diam amet ipsum dolor duo ipsum rebum stet dolor amet diam stet. Est stet ea lorem amet est kasd kasd erat eos'],
            ] as $t)
            <div class="testimonial-item bg-white rounded overflow-hidden">
                <div class="d-flex align-items-center p-4">
                    <img class="img-fluid flex-shrink-0 rounded"
                         src="{{ asset('img/testimonial-1.jpg') }}"
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