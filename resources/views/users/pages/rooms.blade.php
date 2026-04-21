@extends('users.layouts.app')

@section('title', 'Our Rooms - Hotelier')

@section('content')

@include('users.components.page-header', ['title' => 'Hotel Rooms', 'breadcrumb' => 'Rooms'])

{{-- 🟢 BLOK FILTER & SORTING PENCARIAN --}}
<div class="container-fluid bg-white mb-5 wow fadeIn" data-wow-delay="0.1s" style="padding: 35px; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);">
    <div class="container">
        <form action="{{ route('rooms') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-10">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="form-floating date" id="date3" data-target-input="nearest">
                                <input type="text" name="check_in" class="form-control datetimepicker-input" placeholder="Check In" data-target="#date3" data-toggle="datetimepicker" value="{{ request('check_in') }}" />
                                <label>Check In</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating date" id="date4" data-target-input="nearest">
                                <input type="text" name="check_out" class="form-control datetimepicker-input" placeholder="Check Out" data-target="#date4" data-toggle="datetimepicker" value="{{ request('check_out') }}" />
                                <label>Check Out</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <select name="adult" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="1" {{ request('adult') == 1 ? 'selected' : '' }}>1 Dewasa</option>
                                    <option value="2" {{ request('adult') == 2 ? 'selected' : '' }}>2 Dewasa</option>
                                    <option value="3" {{ request('adult') == 3 ? 'selected' : '' }}>3 Dewasa</option>
                                </select>
                                <label>Dewasa</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <select name="child" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="0" {{ request('child') == '0' ? 'selected' : '' }}>0 Anak</option>
                                    <option value="1" {{ request('child') == '1' ? 'selected' : '' }}>1 Anak</option>
                                    <option value="2" {{ request('child') == '2' ? 'selected' : '' }}>2 Anak</option>
                                </select>
                                <label>Anak</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <select name="sort" class="form-select">
                                    <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Terbaru</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Termurah</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Termahal</option>
                                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                                </select>
                                <label>Urutkan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary w-100 h-100 py-2"><i class="fa fa-search me-2"></i>Cari</button>
                    {{-- Tombol Reset untuk mengembalikan ke setelan awal --}}
                    <a href="{{ route('rooms') }}" class="btn btn-dark w-100 py-2"><i class="fa fa-sync-alt me-2"></i>Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- 🟢 AKHIR BLOK FILTER --}}

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
                        <img class="img-fluid w-100" 
                             src="{{ $room->roomType?->foto ? asset($room->roomType->foto) : asset('img/room-1.jpg') }}" 
                             alt="{{ $room->roomType?->name ?? 'Room' }}">
                        <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">
                            Rp {{ number_format($room->roomType?->price ?? 0, 0, ',', '.') }}/Night
                        </small>
                    </div>
                    <div class="p-4 mt-2">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0">{{ $room->roomType?->name ?? 'Tipe Kamar Dihapus' }} <br><span class="fs-6 text-muted">No. {{ $room->room_number }}</span></h5>
                            <div class="ps-2">
                                @for($i = 1; $i <= ($room->roomType?->rating ?? 5); $i++)
                                    <small class="fa fa-star text-primary"></small>
                                @endfor
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            {{-- 🟢 Menambahkan ?-> untuk mencegah error jika roomType kosong --}}
                            <small class="border-end me-3 pe-3">
                                <i class="fa fa-users text-primary me-2"></i>{{ $room->roomType?->adult_capacity ?? 2 }} Adult
                            </small>
                            <small class="border-end me-3 pe-3">
                                <i class="fa fa-baby text-primary me-2"></i>{{ $room->roomType?->child_capacity ?? 1 }} Child
                            </small>
                            <small>
                                <i class="fa fa-bed text-primary me-2"></i>{{ $room->roomType?->bed_type ?? '-' }}
                            </small>
                        </div>
                        <p class="text-body mb-3">{!! Str::limit($room->roomType?->description ?? 'Deskripsi belum tersedia.', 100) !!}</p>
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-sm btn-primary rounded py-2 px-4"
                               href="{{ route('room.detail', $room->id) }}">
                                View Detail
                            </a>
                            {{-- 🟢 Mengirim data tanggal pilihan ke form Booking secara otomatis --}}
                            <a class="btn btn-sm btn-dark rounded py-2 px-4"
                               href="{{ route('booking', ['room' => $room->id, 'check_in' => request('check_in'), 'check_out' => request('check_out')]) }}">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="empty-state">
                    <i class="fa fa-bed fs-1 text-muted mb-3"></i>
                    <p class="text-muted">Maaf, tidak ada kamar yang sesuai dengan pencarian Anda.</p>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($rooms instanceof \Illuminate\Pagination\LengthAwarePaginator && $rooms->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $rooms->links() }}
        </div>
        @endif
    </div>
</div>

@endsection