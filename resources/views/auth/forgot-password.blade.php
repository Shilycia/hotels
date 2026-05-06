<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi – {{ config('hotel.name', 'Hotel Neo') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    <style>
        body { font-family:'Heebo',sans-serif; background:#f8f9fa; min-height:100vh; display:flex; flex-direction:column; }
        .auth-wrapper { flex:1; display:flex; align-items:center; justify-content:center; padding:40px 16px; }
        .auth-card { display:flex; width:100%; max-width:860px; min-height:540px; border-radius:12px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.1); }
        
        /* Left Side */
        .auth-left { width:300px; flex-shrink:0; background:#1a1f2e; display:flex; flex-direction:column; padding:36px 30px; position:relative; overflow:hidden; }
        .auth-left::before { content:''; position:absolute; top:-80px; left:-80px; width:240px; height:240px; border-radius:50%; background:rgba(200,169,110,.07); pointer-events:none; }
        .auth-left::after  { content:''; position:absolute; bottom:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(200,169,110,.05); pointer-events:none; }
        
        .auth-brand { display:flex; align-items:center; gap:10px; margin-bottom:44px; text-decoration:none; z-index: 1; }
        .auth-brand-icon { width:36px; height:36px; background:#c8a96e; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .auth-brand-icon i { font-size:16px; color:#fff; }
        .auth-brand-name { font-size:18px; font-weight:700; color:#fff; letter-spacing:-.3px; }
        
        .auth-illus { flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; z-index: 1; }
        .auth-hotel-icon { width:80px; height:80px; border-radius:50%; background:rgba(200,169,110,.1); border:1px solid rgba(200,169,110,.2); display:flex; align-items:center; justify-content:center; margin-bottom:20px; }
        .auth-hotel-icon i { font-size:32px; color:#c8a96e; opacity:.85; }
        .auth-headline { font-size:18px; font-weight:700; color:#fff; line-height:1.4; margin-bottom:10px; }
        .auth-desc { font-size:12.5px; color:rgba(255,255,255,.4); line-height:1.7; max-width:200px; }
        
        .auth-features { margin-top:28px; list-style:none; padding:0; width:100%; }
        .auth-features li { display:flex; align-items:center; gap:9px; font-size:12px; color:rgba(255,255,255,.45); padding:5px 0; }
        .auth-features li::before { content:''; width:5px; height:5px; border-radius:50%; background:#c8a96e; opacity:.75; flex-shrink:0; }
        
        /* Right Side */
        .auth-right { flex:1; background:#fff; padding:40px; display:flex; flex-direction:column; justify-content:center; }
        .auth-back { display:inline-flex; align-items:center; gap:6px; font-size:12.5px; color:#6c757d; text-decoration:none; margin-bottom:28px; transition:color .15s; }
        .auth-back:hover { color:#c8a96e; }
        .auth-section-tag { font-size:10.5px; font-weight:600; color:#c8a96e; text-transform:uppercase; letter-spacing:1.2px; margin-bottom:6px; }
        .auth-title { font-size:26px; font-weight:700; color:#1a1f2e; margin-bottom:4px; }
        .auth-subtitle { font-size:13px; color:#6c757d; margin-bottom:28px; line-height:1.6; }
        
        .auth-label { display:block; font-size:11px; font-weight:600; color:#344767; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
        .auth-input-group { display:flex; align-items:center; border:1px solid #e0e5ec; border-radius:8px; background:#f8f9fa; overflow:hidden; transition:border-color .2s,box-shadow .2s; }
        .auth-input-group:focus-within { border-color:#c8a96e; background:#fff; box-shadow:0 0 0 3px rgba(200,169,110,.12); }
        .auth-input-group .ig-icon { width:42px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .auth-input-group .ig-icon i { font-size:13px; color:#c8a96e; opacity:.8; }
        .auth-input-group input { flex:1; border:none; background:transparent; font-family:'Heebo',sans-serif; font-size:13.5px; color:#344767; padding:11px 10px 11px 0; outline:none; }
        .auth-input-group input::placeholder { color:#b2bec3; }
        
        .auth-divider { display:flex; align-items:center; gap:12px; margin:12px 0 16px; }
        .auth-divider::before,.auth-divider::after { content:''; flex:1; height:1px; background:#e9ecef; }
        .auth-divider span { font-size:11.5px; color:#adb5bd; white-space:nowrap; }
        
        .btn-primary { background-color: #c8a96e; border-color: #c8a96e; }
        .btn-primary:hover { background-color: #b0925c; border-color: #b0925c; }
        
        @media(max-width:640px){ .auth-left{display:none} .auth-right{padding:28px 20px} }
    </style>
</head>
<body>

<div class="bg-dark py-2 px-4 d-none d-lg-flex align-items-center justify-content-between">
    <small class="text-white-50"><i class="fa fa-phone-alt text-primary me-2" style="font-size:11px"></i>{{ config('hotel.phone', '+62 812 3456 7890') }}</small>
    <small class="text-white-50"><i class="far fa-clock text-primary me-2" style="font-size:11px"></i>{{ config('hotel.hours', 'Layanan 24 Jam') }}</small>
</div>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-left">
            <a href="{{ route('home') }}" class="auth-brand">
                <div class="auth-brand-icon"><i class="fa fa-hotel"></i></div>
                <div class="auth-brand-name">{{ config('hotel.name', 'Hotel Neo') }}</div>
            </a>
            <div class="auth-illus">
                <div class="auth-hotel-icon"><i class="fa fa-unlock-alt"></i></div>
                <div class="auth-headline">Pemulihan Akun<br>Hotel Neo</div>
                <div class="auth-desc">Kami akan membantu Anda mendapatkan kembali akses ke akun Anda.</div>
            </div>
        </div>

        <div class="auth-right">
            <a href="{{ route('guest.login') }}" class="auth-back"><i class="fa fa-arrow-left"></i> Kembali ke Halaman Masuk</a>

            <div class="auth-section-tag">Pemulihan</div>
            <div class="auth-title">Lupa Kata Sandi?</div>
            <div class="auth-subtitle">Masukkan alamat email yang terdaftar pada akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.</div>

            {{-- Pesan Sukses Pengiriman Email (Bawaan Laravel) --}}
            @if(session('status'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4 py-3">
                    <i class="fa fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 py-3">
                    <i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            {{-- Ganti route di bawah dengan route pengiriman email Anda --}}
            <form method="POST" action="{{ route('guest.password.email') }}" id="forgotForm">
                @csrf

                <div class="mb-4">
                    <label class="auth-label" for="email">Alamat Email</label>
                    <div class="auth-input-group">
                        <div class="ig-icon"><i class="fa fa-envelope"></i></div>
                        <input type="email" name="email" id="email" placeholder="email@contoh.com" value="{{ old('email') }}" required autofocus autocomplete="email">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold text-white mt-2" id="submitBtn">
                    <span id="btnLabel"><i class="fa fa-paper-plane me-2"></i>Kirim Tautan Pemulihan</span>
                    <span class="spinner-border spinner-border-sm d-none" id="btnSpinner" role="status"></span>
                </button>
            </form>

        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script>
    document.getElementById('forgotForm').addEventListener('submit', function () {
        document.getElementById('btnLabel').classList.add('d-none');
        document.getElementById('btnSpinner').classList.remove('d-none');
        document.getElementById('submitBtn').disabled = true;
    });
</script>
</body>
</html>