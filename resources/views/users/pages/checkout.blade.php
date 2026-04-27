@extends('users.layouts.app')

@section('title', 'Selesaikan Pesanan – Hotel Neo')

@section('content')

{{-- Page Header --}}
<div class="container-fluid page-header mb-5 p-0" style="background-image: url({{ asset('img/carousel-2.jpg') }}); background-position: center; background-size: cover;">
    <div class="container-fluid page-header-inner py-5" style="background: rgba(15, 15, 17, 0.7);">
        <div class="container text-center pb-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Selesaikan Pesanan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Checkout</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">

        {{-- BLOK PESAN ERROR --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm">
                    <i class="fa fa-exclamation-triangle me-2"></i><strong>Pemesanan Gagal!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm">
                    <i class="fa fa-exclamation-circle me-2"></i><strong>Cek kembali data Anda:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        {{-- AKHIR BLOK PESAN ERROR --}}
        
        <form action="{{ route('booking.store') }}" method="POST">
            @csrf
            <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
            <input type="hidden" name="check_in" value="{{ $checkIn->format('Y-m-d') }}">
            <input type="hidden" name="check_out" value="{{ $checkOut->format('Y-m-d') }}">
            <input type="hidden" name="adults" value="{{ $request->adults }}">
            <input type="hidden" name="children" value="{{ $request->children }}">
            <input type="hidden" name="voucher_code" id="applied_voucher_code" value=""> {{-- Untuk Voucher --}}

            {{-- Bagian Kanan: Ringkasan Pemesanan --}}
                <div class="col-lg-4">
                    <div class="bg-light rounded shadow-sm border p-4 sticky-top" style="top: 100px;">
                        <h4 class="fw-bold mb-4 border-bottom pb-2">Rincian Pesanan</h4>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal ({{ $nights }} Malam)</span>
                            <span class="text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        {{-- Diskon Otomatis --}}
                        <div class="d-flex justify-content-between mb-2 text-success {{ $autoDiscountAmount > 0 ? '' : 'd-none' }}" id="auto-discount-row">
                            <span>Promo Otomatis</span>
                            <span>- Rp {{ number_format($autoDiscountAmount, 0, ',', '.') }}</span>
                        </div>

                        {{-- Diskon Voucher (Muncul via JS) --}}
                        <div class="d-flex justify-content-between mb-2 text-primary d-none" id="voucher-discount-row">
                            <span><i class="fa fa-ticket-alt me-1"></i> Diskon Voucher</span>
                            <span id="display-voucher-discount">- Rp 0</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                            <span class="text-muted">Pajak & Layanan</span>
                            <span class="text-success">Termasuk</span>
                        </div>

                        {{-- FORM INPUT VOUCHER --}}
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Gunakan Kode Voucher</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-uppercase" id="voucher_input" placeholder="Misal: NEO2026">
                                <button class="btn btn-dark" type="button" id="btn-apply-voucher">Pakai</button>
                            </div>
                            <div id="voucher-message" class="small mt-2 fw-bold"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 pt-2 border-top">
                            <span class="fw-bold fs-5">Total Tagihan</span>
                            <span class="fw-bold fs-4 text-primary" id="display-final-total">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase shadow-sm">Konfirmasi & Lanjutkan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    document.getElementById('btn-apply-voucher').addEventListener('click', function() {
        let code = document.getElementById('voucher_input').value;
        let subtotal = {{ $subtotal }};
        let autoDiscountAmount = {{ $autoDiscountAmount }};
        let messageBox = document.getElementById('voucher-message');
        
        if(!code) return messageBox.innerHTML = '<span class="text-danger">Masukkan kode dulu!</span>';

        messageBox.innerHTML = '<span class="text-muted"><i class="fa fa-spinner fa-spin"></i> Mengecek...</span>';

        fetch("{{ route('voucher.apply') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code, subtotal: subtotal, auto_discount_amount: autoDiscountAmount })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                messageBox.innerHTML = `<span class="text-success"><i class="fa fa-check-circle"></i> ${data.message}</span>`;
                
                // Set nilai hidden input untuk disubmit
                document.getElementById('applied_voucher_code').value = code;
                
                // Update UI Harga
                document.getElementById('voucher-discount-row').classList.remove('d-none');
                document.getElementById('display-voucher-discount').innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(data.voucher_amount);
                document.getElementById('display-final-total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.final_total);
                
                // Sembunyikan promo otomatis jika tidak stackable
                if(!data.is_stackable) {
                    let autoRow = document.getElementById('auto-discount-row');
                    if(autoRow) autoRow.classList.add('text-decoration-line-through', 'text-muted');
                }

                document.getElementById('voucher_input').setAttribute('readonly', true);
                document.getElementById('btn-apply-voucher').setAttribute('disabled', true);
            } else {
                messageBox.innerHTML = `<span class="text-danger"><i class="fa fa-times-circle"></i> ${data.message}</span>`;
            }
        });
    });
</script>
@endpush

@endsection