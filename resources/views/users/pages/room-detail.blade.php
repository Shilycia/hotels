@extends('layouts.app')

@section('title', $room->name . ' - Hotelier')

@section('content')

@include('components.page-header', [
    'title' => $room->name,
    'breadcrumb' => $room->name,
    'parent' => 'Rooms',
    'parentUrl' => route('rooms')
])

<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-5">
            {{-- Left: Room Images & Details --}}
            <div class="col-lg-8">
                {{-- Carousel / Main Image --}}
                <div id="roomCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                    <div class="carousel-inner rounded">
                        @foreach($room->images ?? [$room->image] as $index => $img)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ asset($img) }}" class="d-block w-100 rounded" alt="{{ $room->name }}" style="height: 450px; object-fit: cover;">
                        </div>
                        @endforeach
                    </div>
                    @if(($room->images ?? [])->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                    @endif
                </div>

                {{-- Room Info --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2>{{ $room->name }}</h2>
                        <span class="badge bg-primary fs-5">Rp {{ number_format($room->price) }}/Night</span>
                    </div>
                    <div class="d-flex mb-3">
                        <span class="border-end me-3 pe-3">
                            <i class="fa fa-bed text-primary me-2"></i>{{ $room->bed_type }}
                        </span>
                        <span class="border-end me-3 pe-3">
                            <i class="fa fa-bath text-primary me-2"></i>{{ $room->bath_count }} Bath
                        </span>
                        <span class="border-end me-3 pe-3">
                            <i class="fa fa-wifi text-primary me-2"></i>Free Wifi
                        </span>
                        <span>
                            <i class="fa fa-expand-arrows-alt text-primary me-2"></i>{{ $room->size ?? '200' }}m²
                        </span>
                    </div>
                    <div class="mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star {{ $i <= $room->rating ? 'text-primary' : 'text-muted' }}"></i>
                        @endfor
                        <small class="ms-2 text-muted">({{ $room->reviews_count ?? 0 }} reviews)</small>
                    </div>
                </div>

                <h4 class="mb-3">Room Description</h4>
                <p class="text-body mb-4">{!! $room->description !!}</p>

                {{-- Amenities --}}
                <h4 class="mb-3">Room Amenities</h4>
                <div class="row g-3 mb-4">
                    @foreach($room->amenities ?? ['Free WiFi','Air Conditioning','Smart TV','Mini Bar','Room Service','Safe Box','Bathtub','Balcony'] as $amenity)
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-check text-primary me-2"></i>
                            <span>{{ $amenity }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Booking Sidebar --}}
            <div class="col-lg-4">
                <div class="bg-light rounded p-4 sticky-top" style="top: 80px;">
                    <h4 class="mb-4">Book This Room</h4>
                    <form action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room->id }}">

                        <div class="mb-3">
                            <label class="form-label">Check In</label>
                            <div class="date" id="date1" data-target-input="nearest">
                                <input type="text"
                                       name="check_in"
                                       class="form-control datetimepicker-input @error('check_in') is-invalid @enderror"
                                       placeholder="Check In"
                                       data-target="#date1"
                                       data-toggle="datetimepicker"
                                       value="{{ old('check_in') }}" />
                                @error('check_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Check Out</label>
                            <div class="date" id="date2" data-target-input="nearest">
                                <input type="text"
                                       name="check_out"
                                       class="form-control datetimepicker-input @error('check_out') is-invalid @enderror"
                                       placeholder="Check Out"
                                       data-target="#date2"
                                       data-toggle="datetimepicker"
                                       value="{{ old('check_out') }}" />
                                @error('check_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adult</label>
                            <select class="form-select" name="adult">
                                <option value="1" {{ old('adult') == 1 ? 'selected' : '' }}>1 Adult</option>
                                <option value="2" {{ old('adult') == 2 ? 'selected' : '' }}>2 Adults</option>
                                <option value="3" {{ old('adult') == 3 ? 'selected' : '' }}>3 Adults</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Child</label>
                            <select class="form-select" name="child">
                                <option value="0" {{ old('child') == 0 ? 'selected' : '' }}>No Child</option>
                                <option value="1" {{ old('child') == 1 ? 'selected' : '' }}>1 Child</option>
                                <option value="2" {{ old('child') == 2 ? 'selected' : '' }}>2 Children</option>
                            </select>
                        </div>

                        <div class="border-top pt-3 mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Price per night</span>
                                <span class="fw-bold">Rp {{ number_format($room->price) }}</span>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 py-3" type="submit">Book Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection