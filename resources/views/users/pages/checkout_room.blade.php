@extends('users.layouts.app')

@section('title', 'Checkout Kamar - Hotel Neo')

@section('content')
<div class="container-fluid page-header mb-5 p-0" style="background-image: url({{ asset('img/carousel-1.jpg') }}); background-position: center; background-size: cover;">
    <div class="container-fluid page-header-inner py-5" style="background: rgba(15, 15, 17, 0.7);">
        <div class="container text-center pb-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Selesaikan Reservasi</h1>
        </div>
    </div>
</div>

<div class="container py-5">
    <form action="{{ route('booking.store') }}" method="POST">
        @csrf
        <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
        <input type="hidden" name="check_in" value="{{ $request->check_in }}">
        <input type="hidden" name="check_out" value="{{ $request->check_out }}">

        <div class="row g-5">
            {{-- Kolom Kiri: Form Data & Permintaan --}}
            <div class="col-lg-7">
                <div class="bg-light rounded p-4 p-sm-5 mb-4 shadow-sm border">
                    <h4 class="fw-bold mb-4">Informasi Pemesan</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ $guest->name }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Email</label>
                            <input type="email" class="form-control" value="{{ $guest->email }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Nomor Telepon</label>
                            <input type="text" class="form-control" value="{{ $guest->phone ?? '-' }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Tamu</label>
                            <input type="text" class="form-control" value="{{ $request->adults }} Dewasa, {{ $request->children ?? 0 }} Anak" readonly disabled>
                        </div>
                    </div>
                </div>

                <div class="bg-light rounded p-4 p-sm-5 shadow-sm border">
                    <h4 class="fw-bold mb-4">Permintaan Khusus (Opsional)</h4>
                    <textarea name="special_request" class="form-control" rows="4" placeholder="Misal: Ranjang tambahan, lantai atas, dsb..."></textarea>
                    <p class="text-muted small mt-2">*Permintaan khusus bergantung pada ketersediaan saat check-in.</p>
                </div>
            </div>

            {{-- Kolom Kanan: Ringkasan Harga --}}
            <div class="col-lg-5">
                <div class="bg-white rounded p-4 p-sm-5 shadow-sm border">
                    <h4 class="fw-bold mb-4">Ringkasan Pesanan</h4>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tipe Kamar</span>
                        <strong class="text-dark">{{ $roomType->name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Check-In</span>
                        <strong class="text-dark">{{ $checkIn->format('d M Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span>Check-Out</span>
                        <strong class="text-dark">{{ $checkOut->format('d M Y') }}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2 mt-3">
                        <span>Tarif ({{ $nights }} Malam)</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($autoDiscountAmount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Diskon Otomatis</span>
                        <span>- Rp {{ number_format($autoDiscountAmount, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    <div class="mt-3 pt-3 border-top">
                        <label class="form-label text-muted small fw-bold text-uppercase">Punya Kode Voucher?</label>
                        <div class="input-group mb-4">
                            <input type="text" name="voucher_code" class="form-control" placeholder="Masukkan kode promo">
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded border">
                            <span class="fw-bold text-uppercase">Total Bayar</span>
                            <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($totalPrice, 0, ',', '.') }}</h3>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mt-4 fw-bold"><i class="fa fa-lock me-2"></i>Lanjutkan ke Pembayaran</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection