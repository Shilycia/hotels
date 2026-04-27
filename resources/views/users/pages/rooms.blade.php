@extends('users.layouts.app')

@section('title', 'Katalog Kamar – Hotel Neo')

@section('content')

{{-- Page Header --}}
<div class="container-fluid page-header mb-5 p-0" style="background-image: url({{ asset('img/carousel-1.jpg') }}); background-position: center; background-size: cover;">
    <div class="container-fluid page-header-inner py-5" style="background: rgba(15, 15, 17, 0.7);">
        <div class="container text-center pb-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Kamar Kami</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Kamar</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

{{-- 1. FORM PENCARIAN --}}
<div class="container-fluid bg-white mb-5 wow fadeIn" data-wow-delay="0.1s" style="padding: 35px; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);">
    <div class="container">
        <form action="{{ route('rooms.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-10">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="check_in" class="form-control" value="{{ request('check_in') }}" min="{{ date('Y-m-d') }}" />
                                <label>Check In</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="check_out" class="form-control" value="{{ request('check_out') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                                <label>Check Out</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="adults" class="form-select">
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
                                <select name="children" class="form-select">
                                    <option value="">Semua</option>
                                    @for($i=0; $i<=5; $i++)
                                        <option value="{{ $i }}" {{ request('children') == $i ? 'selected' : '' }}>{{ $i }} Anak</option>
                                    @endfor
                                </select>
                                <label>Anak-anak</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary w-100 h-100 py-2 fw-bold"><i class="fa fa-search me-2"></i>Cari</button>
                    <a href="{{ route('rooms.index') }}" class="btn btn-dark w-100 py-2"><i class="fa fa-sync-alt me-2"></i>Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- 2. DAFTAR KAMAR --}}
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Katalog Kamar</h6>
            <h1 class="mb-5">Jelajahi Kamar <span class="text-primary text-uppercase">Mewah</span> Kami</h1>
        </div>
        
        <div class="row g-4">
            @forelse($roomTypes as $room)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="room-item shadow rounded overflow-hidden bg-white h-100 d-flex flex-column">
                    <div class="position-relative">
                        <img class="img-fluid w-100" 
                             src="{{ $room->foto ? asset('storage/' . $room->foto) : asset('img/room-1.jpg') }}" 
                             alt="{{ $room->name }}" style="height: 250px; object-fit: cover;">
                        <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4 fw-bold shadow-sm">
                            Rp {{ number_format($room->price, 0, ',', '.') }} <span class="fw-normal">/ Malam</span>
                        </small>
                    </div>
                    <div class="p-4 mt-3 d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold">{{ $room->name }}</h5>
                            <div class="ps-2">
                                @for($i = 1; $i <= ($room->rating ?? 5); $i++)
                                    <small class="fa fa-star text-primary"></small>
                                @endfor
                            </div>
                        </div>
                        <div class="d-flex mb-3 text-muted small flex-wrap gap-2">
                            <span class="border-end pe-2">
                                <i class="fa fa-users text-primary me-1"></i>{{ $room->adult_capacity }} Dws
                            </span>
                            <span class="border-end pe-2">
                                <i class="fa fa-baby text-primary me-1"></i>{{ $room->child_capacity }} Ank
                            </span>
                            <span>
                                <i class="fa fa-bed text-primary me-1"></i>{{ $room->bed_type ?? '-' }}
                            </span>
                        </div>
                        <p class="text-body mb-4 flex-grow-1">{{ Str::limit($room->description, 100) }}</p>
                        
                        <div class="d-flex justify-content-between mt-auto">
                            <a class="btn btn-sm btn-outline-primary rounded py-2 px-3"
                               href="{{ route('rooms.show', $room->id) }}">
                                Detail Kamar
                            </a>
                            
                            {{-- Parameter Default untuk mencegah Error di Halaman Checkout --}}
                            @php
                                $cekIn = request('check_in') ?? date('Y-m-d');
                                $cekOut = request('check_out') ?? date('Y-m-d', strtotime('+1 day'));
                                $dws = request('adults') ?? 1;
                                $ank = request('children') ?? 0;
                            @endphp
                            
                            <a class="btn btn-sm btn-primary rounded py-2 px-3 fw-bold"
                            href="{{ route('rooms.show', ['id' => $room->id, 'check_in' => request('check_in'), 'check_out' => request('check_out'), 'adults' => request('adults'), 'children' => request('children')]) }}">
                                Pesan Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="fa fa-search fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted">Kamar Tidak Ditemukan</h4>
                <p class="text-muted">Maaf, tidak ada tipe kamar yang sesuai dengan pencarian Anda saat ini.</p>
                <a href="{{ route('rooms.index') }}" class="btn btn-primary mt-2">Lihat Semua Kamar</a>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection