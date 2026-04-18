@extends('layouts.app')

@section('title', 'Our Rooms - Hotelier')

@section('content')

@include('components.page-header', ['title' => 'Hotel Rooms', 'breadcrumb' => 'Rooms'])

<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Rooms</h6>
            <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Luxurious</span> Rooms</h1>
        </div>
        <div class="row g-4">
            @forelse($rooms as $room)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="room-item shadow rounded overflow-hidden">
                    <div class="position-relative">
                        <img class="img-fluid" src="{{ asset($room->image) }}" alt="{{ $room->name }}">
                        <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">
                            Rp {{ number_format($room->price) }}/Night
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
            <div class="col-12 text-center py-5">
                <p class="text-muted">No rooms available at the moment.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if(isset($rooms) && $rooms->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $rooms->links() }}
        </div>
        @endif
    </div>
</div>

@endsection