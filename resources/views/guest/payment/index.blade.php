<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selesaikan Pembayaran - Hotel Neo</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Lora:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'DM Sans', sans-serif; 
            background: #f9f8f6; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .card { 
            background: white; 
            padding: 40px 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            text-align: center; 
            max-width: 400px; 
            width: 100%; 
            border: 1px solid #e8e6e1;
        }
        .title {
            font-family: 'Lora', serif;
            font-size: 22px;
            color: #2c2420;
            margin-bottom: 5px;
        }
        .desc {
            font-size: 13px;
            color: #8b7355;
            margin-bottom: 25px;
        }
        .amount-box {
            background: #f9f8f6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .amount-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #8b7355;
            margin-bottom: 8px;
        }
        .amount { 
            font-size: 28px; 
            font-weight: 700; 
            color: #2c2420; 
        }
        .btn-pay { 
            background: #c07850; 
            color: white; 
            border: none; 
            padding: 14px 24px; 
            border-radius: 6px; 
            font-size: 15px; 
            font-weight: 500;
            cursor: pointer; 
            width: 100%; 
            transition: all 0.3s ease; 
        }
        .btn-pay:hover { 
            background: #a66541; 
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="title">Selesaikan Tagihan</div>
        <div class="desc">ID Transaksi: #PAY-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</div>
        
        <div class="amount-box">
            <div class="amount-label">Total Pembayaran</div>
            <div class="amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
        </div>
        
        <button id="pay-button" class="btn-pay">Pilih Metode Pembayaran</button>
    </div>

    <script>
    function updateDatabaseStatus(status, redirectUrl = null) {
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/pay/{{ $payment->id }}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    document.getElementById('pay-button').onclick = function () {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
                alert("Pembayaran Berhasil! Terima kasih.");
                updateDatabaseStatus('paid', '/admin/payments'); 
            },
            onPending: function(result){
                alert("Menunggu pembayaran Anda. Silakan selesaikan instruksi pembayaran.");
                updateDatabaseStatus('pending', '/admin/payments');
            },
            onError: function(result){
                alert("Maaf, pembayaran Anda gagal. Silakan coba lagi.");
                updateDatabaseStatus('failed');
            },
            onClose: function(){
                alert('Anda menutup jendela pembayaran sebelum menyelesaikannya.');
            }
        });
    };
</script>
</body>
</html>