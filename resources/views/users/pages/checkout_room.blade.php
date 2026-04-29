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
                        <span id="base-subtotal" data-value="{{ $subtotal }}">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($autoDiscountAmount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Diskon Otomatis</span>
                        <span id="auto-discount" data-value="{{ $autoDiscountAmount }}">- Rp {{ number_format($autoDiscountAmount, 0, ',', '.') }}</span>
                    </div>
                    @else
                    <div class="d-flex justify-content-between mb-2 text-success d-none" id="auto-discount-container">
                        <span>Diskon Otomatis</span>
                        <span id="auto-discount" data-value="0">- Rp 0</span>
                    </div>
                    @endif

                    {{-- Elemen untuk memunculkan hasil voucher --}}
                    <div class="d-flex justify-content-between mb-2 text-success d-none" id="voucher-discount-container">
                        <span>Voucher Promo</span>
                        <span id="voucher-discount-amount">- Rp 0</span>
                    </div>

                    <div class="mt-3 pt-3 border-top">
                        <label class="form-label text-muted small fw-bold text-uppercase">Punya Kode Voucher?</label>
                        {{-- [B-09] FIX: Tambahkan tombol Apply dan ID --}}
                        <div class="input-group mb-2">
                            <input type="text" id="voucher_code_input" name="voucher_code" class="form-control" placeholder="Masukkan kode promo">
                            <button class="btn btn-outline-primary" type="button" id="btn-apply-voucher">Gunakan</button>
                        </div>
                        <small id="voucher-message" class="d-block mb-3"></small>
                        
                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded border">
                            <span class="fw-bold text-uppercase">Total Bayar</span>
                            <h3 class="fw-bold text-primary mb-0" id="final-total" data-value="{{ $totalPrice }}">Rp {{ number_format($totalPrice, 0, ',', '.') }}</h3>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mt-4 fw-bold"><i class="fa fa-lock me-2"></i>Lanjutkan ke Pembayaran</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

{{-- [B-09] FIX: Tambahkan Script AJAX --}}
@push('scripts')
<script>
    document.getElementById('btn-apply-voucher').addEventListener('click', function() {
        const code = document.getElementById('voucher_code_input').value;
        const messageEl = document.getElementById('voucher-message');
        const voucherContainer = document.getElementById('voucher-discount-container');
        const voucherAmountEl = document.getElementById('voucher-discount-amount');
        const finalTotalEl = document.getElementById('final-total');
        
        if(!code) {
            messageEl.innerHTML = '<span class="text-danger">Silakan masukkan kode voucher.</span>';
            return;
        }

        messageEl.innerHTML = '<span class="text-info">Memeriksa kode...</span>';

        fetch('{{ route('voucher.apply') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                code: code,
                room_type_id: '{{ $roomType->id }}',
                check_in: '{{ $request->check_in }}',
                check_out: '{{ $request->check_out }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                messageEl.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> ' + data.message + '</span>';
                
                voucherContainer.classList.remove('d-none');
                voucherAmountEl.innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(data.voucher_amount);
                
                finalTotalEl.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.final_total);
                
                if(!data.is_stackable) {
                    const autoContainer = document.getElementById('auto-discount');
                    if(autoContainer) {
                        autoContainer.parentElement.style.textDecoration = 'line-through';
                        autoContainer.parentElement.style.opacity = '0.5';
                    }
                }
            } else {
                messageEl.innerHTML = '<span class="text-danger"><i class="fa fa-times-circle"></i> ' + data.message + '</span>';
                voucherContainer.classList.add('d-none');
                
                // Kembalikan ke harga semula
                const baseTotal = document.getElementById('final-total').getAttribute('data-value');
                finalTotalEl.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(baseTotal);
            }
        })
        .catch(error => {
            messageEl.innerHTML = '<span class="text-danger">Terjadi kesalahan pada server.</span>';
        });
    });
</script>
@endpush