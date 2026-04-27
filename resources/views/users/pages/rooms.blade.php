@extends('users.layouts.app')

@section('title', 'Our Rooms - Hotelier')

@section('content')

@include('users.components.page-header', ['title' => 'Hotel Rooms', 'breadcrumb' => 'Rooms'])

{{-- 1. FORM SEARCH: Disesuaikan dengan controller (adults/children) --}}
<div class="container-fluid bg-white mb-5 wow fadeIn" data-wow-delay="0.1s" style="padding: 35px; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);">
    <div class="container">
        {{-- Menggunakan route: rooms.index --}}
        <form action="{{ route('rooms.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-10">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="check_in" class="form-control" value="{{ request('check_in') }}" />
                                <label>Check In</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="check_out" class="form-control" value="{{ request('check_out') }}" />
                                <label>Check Out</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="adults" class="form-select"> {{-- Diubah ke 'adults' --}}
                                    <option value="">Semua</option>
                                    @for($i=1; $i<=5; $i++)
                                        <option value="{{ $i }}" {{ request('adults') == $i ? 'selected' : '' }}>{{ $i }} Dewasa</option>
                                    @endfor
                                </select>
                                <label>Dewasa</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="children" class="form-select"> {{-- Diubah ke 'children' --}}
                                    <option value="">Semua</option>
                                    @for($i=0; $i<=5; $i++)
                                        <option value="{{ $i }}" {{ request('children') == $i ? 'selected' : '' }}>{{ $i }} Anak</option>
                                    @endfor
                                </select>
                                <label>Anak</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary w-100 h-100 py-2"><i class="fa fa-search me-2"></i>Cari</button>
                    {{-- Reset kembali ke rooms.index --}}
                    <a href="{{ route('rooms.index') }}" class="btn btn-dark w-100 py-2"><i class="fa fa-sync-alt me-2"></i>Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Rooms</h6>
            <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Luxurious</span> Rooms</h1>
        </div>
        <div class="row g-4">
            {{-- Menggunakan $roomTypes dari PageController@roomCatalog --}}
            @forelse($roomTypes as $room)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="room-item shadow rounded overflow-hidden">
                    <div class="position-relative">
                        <img class="img-fluid w-100" 
                            src="{{ $room->foto ? asset('storage/' . $room->foto) : asset('img/room-1.jpg') }}" 
                            alt="{{ $room->name }}">
                        <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">
                            Rp {{ number_format($room->price, 0, ',', '.') }}/Night
                        </small>
                    </div>
                    <div class="p-4 mt-2">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0">{{ $room->name }}</h5>
                            <div class="ps-2">
                                @for($i = 1; $i <= ($room->rating ?? 5); $i++)
                                    <small class="fa fa-star text-primary"></small>
                                @endfor
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <small class="border-end me-3 pe-3">
                                <i class="fa fa-users text-primary me-2"></i>{{ $room->adult_capacity }} Adult
                            </small>
                            <small class="border-end me-3 pe-3">
                                <i class="fa fa-baby text-primary me-2"></i>{{ $room->child_capacity }} Child
                            </small>
                            <small>
                                <i class="fa fa-bed text-primary me-2"></i>{{ $room->bed_type ?? '-' }}
                            </small>
                        </div>
                        <p class="text-body mb-3">{!! Str::limit($room->description, 100) !!}</p>
                        <div class="d-flex justify-content-between">
                            {{-- 2. ROUTE DETAIL: Sesuai web.php 'rooms.show' --}}
                            <a class="btn btn-sm btn-primary rounded py-2 px-4"
                               href="{{ route('rooms.show', $room->id) }}">
                                View Detail
                            </a>
                            {{-- 3. ROUTE BOOKING: Sesuai web.php 'checkout.room' --}}
                            <a class="btn btn-sm btn-dark rounded py-2 px-4"
                               href="{{ route('checkout.room', ['room_type_id' => $room->id, 'check_in' => request('check_in'), 'check_out' => request('check_out')]) }}">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="fa fa-bed fs-1 text-muted mb-3"></i>
                <p class="text-muted">Maaf, tidak ada tipe kamar yang tersedia saat ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection