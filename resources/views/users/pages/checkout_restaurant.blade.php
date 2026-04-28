@extends('users.layouts.app')

@section('title', 'Checkout Restoran – Hotel Neo')

@section('content')

{{-- Page Header --}}
<div class="container-fluid page-header mb-5 p-0" style="background-image: url({{ asset('img/carousel-1.jpg') }}); background-position: center; background-size: cover;">
    <div class="container-fluid page-header-inner py-5" style="background: rgba(15, 15, 17, 0.7);">
        <div class="container text-center pb-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Selesaikan Pesanan F&B</h1>
        </div>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm">
                <i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm">
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('restaurant.order.store') }}" method="POST">
            @csrf
            <input type="hidden" name="voucher_code" id="applied_voucher_code" value="">

            <div class="row g-5">
                
                {{-- Bagian Kiri: Opsi Pengiriman --}}
                <div class="col-lg-7">
                    <div class="bg-white rounded shadow-sm border p-4 p-md-5 mb-4">
                        <h4 class="fw-bold text-dark mb-4 border-bottom pb-3"><i class="fa fa-concierge-bell text-primary me-2"></i>Opsi Penerimaan</h4>
                        
                        <div class="row g-4">
                            {{-- Opsi 1: Dine In --}}
                            <div class="col-12">
                                <label class="border rounded p-3 w-100 d-flex align-items-center" style="cursor: pointer;">
                                    <input class="form-check-input me-3" type="radio" name="order_type" value="dine_in" id="radio_dine_in" required checked onchange="toggleDeliveryOptions()">
                                    <div>
                                        <h6 class="fw-bold mb-1">Makan di Restoran (Dine In)</h6>
                                        <p class="text-muted small mb-0">Pesanan akan dihidangkan di meja Anda.</p>
                                    </div>
                                </label>
                            </div>

                            {{-- Input Nomor Meja (Hanya muncul jika Dine In) --}}
                            <div class="col-12" id="table_number_container">
                                <div class="bg-light p-3 rounded border">
                                    <label class="form-label small fw-bold text-uppercase">Masukkan Nomor Meja Anda</label>
                                    <input type="text" name="table_number" id="table_number_input" class="form-control" placeholder="Contoh: Meja 12">
                                </div>
                            </div>

                            {{-- Opsi 2: Takeaway --}}
                            <div class="col-12">
                                <label class="border rounded p-3 w-100 d-flex align-items-center" style="cursor: pointer;">
                                    <input class="form-check-input me-3" type="radio" name="order_type" value="takeaway" onchange="toggleDeliveryOptions()">
                                    <div>
                                        <h6 class="fw-bold mb-1">Bawa Pulang (Takeaway)</h6>
                                        <p class="text-muted small mb-0">Ambil pesanan di kasir restoran untuk dibawa pulang.</p>
                                    </div>
                                </label>
                            </div>

                            {{-- Opsi 3: Room Service --}}
                            <div class="col-12">
                                <label class="border rounded p-3 w-100 d-flex align-items-center {{ !$activeBooking ? 'bg-light opacity-50' : '' }}" style="cursor: {{ !$activeBooking ? 'not-allowed' : 'pointer' }};">
                                    <input class="form-check-input me-3" type="radio" name="order_type" value="room_service" {{ !$activeBooking ? 'disabled' : '' }} onchange="toggleDeliveryOptions()">
                                    <div>
                                        <h6 class="fw-bold mb-1">Pesan ke Kamar (Room Service)</h6>
                                        @if($activeBooking)
                                            <p class="text-success small fw-bold mb-0">Kamar Anda: {{ $activeBooking->room->room_number ?? 'Belum ada nomor' }}</p>
                                        @else
                                            <p class="text-danger small mb-0">Anda tidak memiliki kamar yang aktif saat ini.</p>
                                        @endif
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Kanan: Keranjang --}}
                <div class="col-lg-5">
                    <div class="bg-light rounded shadow-sm border p-4 sticky-top" style="top: 100px;">
                        <h4 class="fw-bold mb-4 border-bottom pb-2">Keranjang Anda</h4>
                        
                        <div class="mb-4" style="max-height: 300px; overflow-y: auto;">
                            @foreach($cart as $item)
                            <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                                <img src="{{ $item['foto'] ? asset('storage/'.$item['foto']) : asset('img/no-img.jpg') }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0" style="font-size: 14px;">{{ $item['name'] }}</h6>
                                    <span class="text-muted small">{{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                    @if($item['notes'])
                                        <p class="text-muted small mb-0 mt-1" style="font-style: italic;">"{{ $item['notes'] }}"</p>
                                    @endif
                                </div>
                                <span class="fw-bold text-dark small ms-2">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2 text-success {{ $autoDiscountAmount > 0 ? '' : 'd-none' }}" id="auto-discount-row">
                            <span>Promo Otomatis</span>
                            <span>- Rp {{ number_format($autoDiscountAmount, 0, ',', '.') }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2 text-primary d-none" id="voucher-discount-row">
                            <span><i class="fa fa-ticket-alt me-1"></i> Diskon Voucher</span>
                            <span id="display-voucher-discount">- Rp 0</span>
                        </div>

                        <div class="mb-4 mt-3 pt-3 border-top">
                            <label class="form-label text-muted small fw-bold text-uppercase">Miliki Kode Voucher?</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-uppercase" id="voucher_input" placeholder="Misal: MAKAN20">
                                <button class="btn btn-dark" type="button" id="btn-apply-voucher">Pakai</button>
                            </div>
                            <div id="voucher-message" class="small mt-2 fw-bold"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 pt-2 border-top">
                            <span class="fw-bold fs-5">Total Pembayaran</span>
                            <span class="fw-bold fs-4 text-primary" id="display-final-total">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase shadow-sm">Bayar Sekarang</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Logika menampilkan input meja
    function toggleDeliveryOptions() {
        const dineInRadio = document.getElementById('radio_dine_in');
        const tableContainer = document.getElementById('table_number_container');
        const tableInput = document.getElementById('table_number_input');
        
        if (dineInRadio.checked) {
            tableContainer.style.display = 'block';
            tableInput.setAttribute('required', 'true');
        } else {
            tableContainer.style.display = 'none';
            tableInput.removeAttribute('required');
            tableInput.value = '';
        }
    }
    // Panggil saat halaman dimuat
    toggleDeliveryOptions();

    // Logika Voucher AJAX
    document.getElementById('btn-apply-voucher').addEventListener('click', function() {
        let code = document.getElementById('voucher_input').value;
        let subtotal = {{ $subtotal }};
        let autoDiscountAmount = {{ $autoDiscountAmount }};
        let messageBox = document.getElementById('voucher-message');
        
        if(!code) return messageBox.innerHTML = '<span class="text-danger">Masukkan kode dulu!</span>';
        messageBox.innerHTML = '<span class="text-muted"><i class="fa fa-spinner fa-spin"></i> Mengecek...</span>';

        fetch("{{ route('voucher.apply') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ code: code, subtotal: subtotal, auto_discount_amount: autoDiscountAmount, type: 'restaurant_orders' })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                messageBox.innerHTML = `<span class="text-success"><i class="fa fa-check-circle"></i> ${data.message}</span>`;
                document.getElementById('applied_voucher_code').value = code;
                document.getElementById('voucher-discount-row').classList.remove('d-none');
                document.getElementById('display-voucher-discount').innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(data.voucher_amount);
                document.getElementById('display-final-total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.final_total);
                
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