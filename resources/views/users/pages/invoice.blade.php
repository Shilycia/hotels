@extends('layouts.app') 

@section('content')
<div class="container mt-5 text-center">
    <h2>Invoice Pembayaran #{{ $payment->id }}</h2>
    <p>Status: <strong>{{ strtoupper($payment->payment_status) }}</strong></p>
    <h3>Total: Rp {{ number_formatt($payment->amount, 0, ',', '.') }}</h3>

    @if($payment->payment_status == 'pending' && isset($snapToken))
        <button id="pay-button" class="btn btn-success btn-lg mt-3">Bayar Sekarang</button>
        
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
            document.getElementById('pay-button').onclick = function(){
                snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result){
                        updateStatus('paid');
                    },
                    onPending: function(result){
                        alert("Menunggu pembayaran Anda!");
                    },
                    onError: function(result){
                        updateStatus('failed');
                    },
                    onClose: function(){
                        alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                    }
                });
            };

            function updateStatus(status) {
                fetch("{{ route('guest.pay.status', $payment->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ status: status })
                }).then(response => {
                    window.location.reload();
                });
            }
        </script>
    @else
        <a href="{{ route('guest.profile') }}" class="btn btn-primary mt-3">Kembali ke Profil</a>
    @endif
</div>
@endsection