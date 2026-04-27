@extends('users.layouts.app')

@section('title', $roomType->name . ' – Hotel Neo')

@section('content')

{{-- Page Header --}}
<div class="container-fluid page-header mb-5 p-0" style="background-image: url({{ $roomType->foto ? asset('storage/' . $roomType->foto) : asset('img/carousel-1.jpg') }}); background-position: center; background-size: cover;">
    <div class="container-fluid page-header-inner py-5" style="background: rgba(15, 15, 17, 0.7);">
        <div class="container text-center pb-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">{{ $roomType->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}">Katalog Kamar</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Detail</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-5">
            {{-- Bagian Kiri: Gambar & Detail Kamar --}}
            <div class="col-lg-8">
                {{-- Gambar Utama --}}
                <div class="mb-4 overflow-hidden rounded shadow-sm">
                    <img src="{{ $roomType->foto ? asset('storage/' . $roomType->foto) : asset('img/room-1.jpg') }}" 
                         class="d-block w-100" alt="{{ $roomType->name }}" style="height: 450px; object-fit: cover;">
                </div>

                {{-- Info Utama --}}
                <div class="mb-4 bg-white p-4 rounded shadow-sm border">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                        <h2 class="mb-0 fw-bold">{{ $roomType->name }}</h2>
                        <span class="badge bg-primary fs-5 px-3 py-2">Rp {{ number_format($roomType->price, 0, ',', '.') }} <small class="fw-normal">/ Malam</small></span>
                    </div>
                    
                    <div class="d-flex mb-3 flex-wrap gap-3 text-muted">
                        <span class="border-end pe-3">
                            <i class="fa fa-bed text-primary me-2"></i>{{ $roomType->bed_type }}
                        </span>
                        <span class="border-end pe-3">
                            <i class="fa fa-bath text-primary me-2"></i>{{ $roomType->bath_count ?? 1 }} Kamar Mandi
                        </span>
                        <span class="border-end pe-3">
                            <i class="fa fa-users text-primary me-2"></i>Maks {{ $roomType->adult_capacity }} Dewasa
                        </span>
                        <span>
                            <i class="fa fa-baby text-primary me-2"></i>Maks {{ $roomType->child_capacity }} Anak
                        </span>
                    </div>
                    
                    <div class="mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star text-primary"></i>
                        @endfor
                        <small class="ms-2 text-muted">(Sangat Direkomendasikan)</small>
                    </div>
                </div>

                {{-- Deskripsi Kamar --}}
                <div class="bg-white p-4 rounded shadow-sm border mb-4">
                    <h4 class="mb-3 fw-bold border-bottom pb-2">Deskripsi Kamar</h4>
                    <p class="text-body mb-0" style="line-height: 1.8;">{{ $roomType->description }}</p>
                </div>

                {{-- Fasilitas (Amenities) --}}
                <div class="bg-white p-4 rounded shadow-sm border mb-4">
                    <h4 class="mb-3 fw-bold border-bottom pb-2">Fasilitas Lengkap</h4>
                    <div class="row g-3">
                        {{-- Karena kita tidak menyimpan relasi amenities secara khusus di DB saat ini, kita berikan default yang menyesuaikan dengan standar hotel bintang 5 --}}
                        @foreach(['AC Sentral', 'TV Kabel Pintar', 'Wi-Fi Kecepatan Tinggi', 'Pembuat Kopi & Teh', 'Layanan Kamar 24 Jam', 'Brankas Pribadi', 'Pancuran & Bathtub', 'Balkon Pribadi'] as $amenity)
                        <div class="col-6 col-md-4">
                            <div class="d-flex align-items-center text-muted">
                                <i class="fa fa-check-circle text-primary me-2"></i>
                                <span>{{ $amenity }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Formulir Pemesanan --}}
            <div class="col-lg-4">
                <div class="bg-white rounded shadow-sm border p-4 sticky-top" style="top: 100px;">
                    <h4 class="mb-4 fw-bold border-bottom pb-2">Pesan Kamar Ini</h4>
                    
                    {{-- Form ini akan mengarah ke halaman Checkout untuk konfirmasi akhir --}}
                    <form action="{{ route('checkout.room') }}" method="GET">
                        <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Tanggal Check In</label>
                            <input type="date" name="check_in" class="form-control bg-light" required min="{{ date('Y-m-d') }}" value="{{ request('check_in', date('Y-m-d')) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Tanggal Check Out</label>
                            <input type="date" name="check_out" class="form-control bg-light" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}">
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Dewasa</label>
                                <select class="form-select bg-light" name="adults">
                                    @for($i = 1; $i <= $roomType->adult_capacity; $i++)
                                        <option value="{{ $i }}">{{ $i }} Orang</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Anak-anak</label>
                                <select class="form-select bg-light" name="children">
                                    @for($i = 0; $i <= $roomType->child_capacity; $i++)
                                        <option value="{{ $i }}">{{ $i }} Orang</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="border-top pt-3 mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Harga per malam</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($roomType->price, 0, ',', '.') }}</span>
                            </div>
                            @if($activeDiscounts->count() > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-success"><i class="fa fa-tags me-1"></i> Diskon Tersedia!</span>
                                    <span class="badge bg-success">Otomatis Terpotong di Checkout</span>
                                </div>
                            @endif
                        </div>

                        @if(session()->has('guest_id'))
                            <button class="btn btn-primary w-100 py-3 fw-bold text-uppercase" type="submit">Lanjutkan ke Pembayaran</button>
                        @else
                            <div class="alert alert-warning small py-2 mb-3">
                                <i class="fa fa-info-circle me-1"></i> Anda harus masuk (login) untuk memesan.
                            </div>
                            <a href="{{ route('guest.login') }}" class="btn btn-outline-primary w-100 py-2">Masuk ke Akun</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection