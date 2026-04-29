@extends('users.layouts.app')

@section('title', 'Complete Your Payment - Hotelier')

@section('content')

@include('users.components.page-header', ['title' => 'Payment', 'breadcrumb' => 'Checkout'])

<div class="container-fluid py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="bg-white shadow rounded p-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                            <i class="fa fa-wallet text-white fs-3"></i>
                        </div>
                        <h2 class="mb-1">Order Summary</h2>
                        <p class="text-muted">Please review your details and complete the payment.</p>
                    </div>

                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Order ID</span>
                            <span class="fw-bold text-dark">#PAY-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Guest Name</span>
                            {{-- [B-07] FIX: Menampilkan nama tamu dari semua jenis pesanan --}}
                            <span class="fw-bold text-dark">{{ $payment->booking->guest->name ?? $payment->restaurantOrder->guest->name ?? $payment->packageOrder->guest->name ?? 'Tamu Hotel Neo' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Type</span>
                            {{-- [B-06] FIX: Menambahkan label khusus untuk Package Order --}}
                            <span class="fw-bold text-dark">{{ $payment->booking_id ? 'Room Booking' : ($payment->restaurant_order_id ? 'Restaurant Order' : 'Package Order') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Status</span>
                            @if($payment->payment_status === 'paid')
                                <span class="badge bg-success">PAID</span>
                            @else
                                <span class="badge bg-warning text-dark">PENDING</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Total Amount</h5>
                        <h3 class="mb-0 text-primary">Rp {{ number_format($payment->amount, 0, ',', '.') }}</h3>
                    </div>

                    @if($payment->payment_status === 'pending')
                        <button id="pay-button" class="btn btn-primary w-100 py-3 fw-bold">
                            <i class="fa fa-credit-card me-2"></i> Pay Now
                        </button>
                        <p class="text-center text-muted small mt-3"><i class="fa fa-lock me-1"></i> Secured by Midtrans</p>
                    @else
                        <div class="alert alert-success text-center">
                            <i class="fa fa-check-circle me-2"></i> This order has been successfully paid.
                        </div>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary w-100 py-3">Back to Home</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    const payButton = document.getElementById('pay-button');
    if (payButton) {
        payButton.addEventListener('click', function () {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    updatePaymentStatus('paid');
                },
                onPending: function(result) {
                    updatePaymentStatus('pending');
                },
                onError: function(result) {
                    updatePaymentStatus('failed');
                },
                onClose: function() {
                    alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
                }
            });
        });
    }

    function updatePaymentStatus(status) {
        fetch("{{ route('guest.pay.status', $payment->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupdate status ke server.');
        });
    }
</script>
@endpush