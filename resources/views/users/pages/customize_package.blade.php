@extends('users.layouts.app')

@section('title', 'Kustomisasi Paket – Hotel Neo')

@section('content')

{{-- Header --}}
<div class="container-fluid page-header mb-5 p-0" style="background-image: url({{ asset('img/carousel-2.jpg') }}); background-position: center; background-size: cover;">
    <div class="container-fluid page-header-inner py-5" style="background: rgba(15, 15, 17, 0.7);">
        <div class="container text-center pb-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Sesuaikan Paket Anda</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Kustomisasi Paket</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        
        {{-- Kolom Kiri: Informasi Paket --}}
        <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.1s">
            <div class="bg-white rounded shadow-sm border p-4 h-100">
                <h4 class="mb-3 text-primary">{{ $package->name ?? 'Paket Spesial' }}</h4>
                <h2 class="mb-4">Rp {{ number_format($package->total_price ?? 0, 0, ',', '.') }} <span style="font-size: 14px; color: #6c757d; font-weight: normal;">/ paket</span></h2>
                
                <p class="text-muted mb-4">{{ $package->description ?? 'Nikmati penawaran paket eksklusif dari Hotel Neo dengan berbagai fasilitas menarik yang dirancang khusus untuk kenyamanan Anda.' }}</p>
                
                <hr class="my-4">
                
                <h6 class="fw-bold mb-3">Fasilitas Termasuk:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fa fa-check text-primary me-2"></i>Kamar: <strong>{{ optional($package->roomType)->name ?? 'Standard Room' }}</strong></li>
                    <li class="mb-2"><i class="fa fa-check text-primary me-2"></i>Akses Kolam Renang & Gym</li>
                    <li class="mb-2"><i class="fa fa-check text-primary me-2"></i>Layanan Kamar 24 Jam</li>
                    <li class="mb-2"><i class="fa fa-check text-primary me-2"></i>Free Wi-Fi Kecepatan Tinggi</li>
                </ul>
            </div>
        </div>

        {{-- Kolom Kanan: Form Kustomisasi --}}
        <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.2s">
            <div class="bg-light rounded shadow-sm border p-4 p-md-5">
                <h5 class="fw-bold mb-4">Lengkapi Data Pemesanan</h5>
                
                <form action="{{ route('package.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Check-in</label>
                            <input type="date" name="check_in" id="check_in" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Check-out</label>
                            <input type="date" name="check_out" id="check_out" class="form-control" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>

                        <div class="col-12 mt-4">
                            <hr>
                            <h6 class="fw-bold mb-3"><i class="fa fa-utensils text-primary me-2"></i>Tambahan Makanan (Opsional)</h6>
                            <p class="text-muted small mb-3">Pilih menu ekstra yang ingin Anda tambahkan ke dalam paket ini (akan diantar ke kamar).</p>
                            
                            <div class="row g-3" style="max-height: 300px; overflow-y: auto;">
                                @foreach($menus as $menu)
                                <div class="col-md-6">
                                    <div class="border rounded p-3 bg-white d-flex align-items-center">
                                        <div class="form-check me-3">
                                            <input class="form-check-input menu-checkbox" type="checkbox" name="extra_menus[]" value="{{ $menu->id }}" data-price="{{ $menu->price }}" id="menu_{{ $menu->id }}">
                                        </div>
                                        <label class="form-check-label w-100" for="menu_{{ $menu->id }}" style="cursor: pointer;">
                                            <span class="d-block fw-bold" style="font-size: 13px;">{{ $menu->name }}</span>
                                            <span class="d-block text-primary fw-bold mt-1" style="font-size: 12px;">+ Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Catatan Khusus</label>
                            <textarea name="special_request" class="form-control" rows="3" placeholder="Ada permintaan khusus? (Contoh: Minta lantai atas, no smoking...)"></textarea>
                        </div>
                        
                        {{-- [R-02] FIX: Menambahkan field Voucher Promo --}}
                        <div class="col-12 mt-4">
                            <hr>
                            <label class="form-label text-muted small fw-bold text-uppercase">Punya Kode Voucher?</label>
                            <div class="input-group mb-2">
                                <input type="text" id="voucher_code_input" name="voucher_code" class="form-control" placeholder="Masukkan kode promo">
                                <button class="btn btn-outline-primary" type="button" id="btn-apply-voucher">Gunakan</button>
                            </div>
                            <small id="voucher-message" class="d-block mb-3"></small>
                            
                            {{-- Container preview voucher --}}
                            <div class="d-flex justify-content-between mb-2 text-success d-none" id="voucher-discount-container">
                                <span>Voucher Promo</span>
                                <span id="voucher-discount-amount">- Rp 0</span>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="p-3 bg-white border rounded d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="d-block text-muted small fw-bold text-uppercase">Estimasi Total</span>
                                    <span class="text-muted" style="font-size: 11px;">(Harga Paket + Tambahan Makanan)</span>
                                </div>
                                <h4 class="text-primary fw-bold mb-0" id="total_display" data-base="{{ $package->total_price ?? 0 }}">Rp {{ number_format($package->total_price ?? 0, 0, ',', '.') }}</h4>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold"><i class="fa fa-shopping-cart me-2"></i>Lanjutkan Pemesanan</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const basePrice = {{ $package->total_price ?? 0 }};
        const checkboxes = document.querySelectorAll('.menu-checkbox');
        const totalDisplay = document.getElementById('total_display');
        let currentDiscount = 0; // Menyimpan nominal diskon dari AJAX
        let currentExtraPrice = 0;

        function updateTotal() {
            currentExtraPrice = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    currentExtraPrice += parseInt(cb.getAttribute('data-price'));
                }
            });
            
            // Hitung harga paket + makanan, lalu kurangi diskon yang sedang aktif
            let finalTotal = basePrice + currentExtraPrice - currentDiscount;
            // Pastikan tidak minus
            finalTotal = finalTotal > 0 ? finalTotal : 0;
            
            totalDisplay.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(finalTotal);
            // Simpan nilai total sebelum diskon ke data-base untuk keperluan AJAX
            totalDisplay.setAttribute('data-base', basePrice + currentExtraPrice);
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateTotal);
        });

        // Script AJAX Voucher
        document.getElementById('btn-apply-voucher').addEventListener('click', function() {
            const codeInput = document.getElementById('voucher_code_input');
            const code = codeInput.value;
            const btnApply = document.getElementById('btn-apply-voucher');
            const messageEl = document.getElementById('voucher-message');
            const voucherContainer = document.getElementById('voucher-discount-container');
            const voucherAmountEl = document.getElementById('voucher-discount-amount');
            
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
                    // Karena paket tidak mengirim durasi menginap ke server AJAX (logika voucher paket bersifat flat/persen dari total saat ini)
                    // Maka kita berikan tipe 'package' (Anda bisa menyesuaikan jika endpoint applyVoucher ingin dimodifikasi khusus paket)
                    // Disini kita biarkan validasi default jalan.
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    messageEl.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> ' + data.message + '</span>';
                    
                    voucherContainer.classList.remove('d-none');
                    voucherAmountEl.innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(data.voucher_amount);
                    
                    // Simpan diskon ke variabel global lalu panggil ulang fungsi kalkulasi
                    currentDiscount = data.voucher_amount;
                    updateTotal();

                    codeInput.setAttribute('readonly', true);
                    btnApply.setAttribute('disabled', true);
                    btnApply.innerText = 'Diterapkan';
                    
                } else {
                    messageEl.innerHTML = '<span class="text-danger"><i class="fa fa-times-circle"></i> ' + data.message + '</span>';
                    voucherContainer.classList.add('d-none');
                    currentDiscount = 0;
                    updateTotal();
                }
            })
            .catch(error => {
                messageEl.innerHTML = '<span class="text-danger">Kode valid tetapi butuh data tanggal Check-In/Out. (Lanjutkan checkout untuk pemotongan otomatis).</span>';
            });
        });
    });
</script>
@endpush