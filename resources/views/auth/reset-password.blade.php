<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atur Ulang Kata Sandi – {{ config('hotel.name', 'Hotel Neo') }}</title>
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
        
        /* Right Side */
        .auth-right { flex:1; background:#fff; padding:40px; display:flex; flex-direction:column; justify-content:center; }
        .auth-section-tag { font-size:10.5px; font-weight:600; color:#c8a96e; text-transform:uppercase; letter-spacing:1.2px; margin-bottom:6px; }
        .auth-title { font-size:26px; font-weight:700; color:#1a1f2e; margin-bottom:4px; }
        .auth-subtitle { font-size:13px; color:#6c757d; margin-bottom:28px; }
        
        .auth-label { display:block; font-size:11px; font-weight:600; color:#344767; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
        .auth-input-group { display:flex; align-items:center; border:1px solid #e0e5ec; border-radius:8px; background:#f8f9fa; overflow:hidden; transition:border-color .2s,box-shadow .2s; }
        .auth-input-group:focus-within { border-color:#c8a96e; background:#fff; box-shadow:0 0 0 3px rgba(200,169,110,.12); }
        .auth-input-group .ig-icon { width:42px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .auth-input-group .ig-icon i { font-size:13px; color:#c8a96e; opacity:.8; }
        .auth-input-group input { flex:1; border:none; background:transparent; font-family:'Heebo',sans-serif; font-size:13.5px; color:#344767; padding:11px 10px 11px 0; outline:none; }
        .auth-input-group input::placeholder { color:#b2bec3; }
        
        .auth-input-group .ig-toggle { width:40px; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; }
        .auth-input-group .ig-toggle i { font-size:13px; color:#adb5bd; transition:color .15s; }
        .auth-input-group .ig-toggle:hover i { color:#c8a96e; }
        
        .btn-primary { background-color: #c8a96e; border-color: #c8a96e; }
        .btn-primary:hover { background-color: #b0925c; border-color: #b0925c; }
        
        @media(max-width:640px){ .auth-left{display:none} .auth-right{padding:28px 20px} }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-left">
            <a href="{{ route('home') }}" class="auth-brand">
                <div class="auth-brand-icon"><i class="fa fa-hotel"></i></div>
                <div class="auth-brand-name">{{ config('hotel.name', 'Hotel Neo') }}</div>
            </a>
            <div class="auth-illus">
                <div class="auth-hotel-icon"><i class="fa fa-key"></i></div>
                <div class="auth-headline">Amankan Akun Anda</div>
                <div class="auth-desc">Gunakan kata sandi yang kuat untuk menjaga keamanan data reservasi Anda.</div>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-section-tag">Keamanan</div>
            <div class="auth-title">Atur Ulang Kata Sandi</div>
            <div class="auth-subtitle">Silakan buat kata sandi baru untuk akun Anda.</div>

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 py-2">
                    <i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            {{-- Ganti route di bawah dengan route proses reset password Anda --}}
            <form method="POST" action="{{ route('guest.password.update') }}" id="resetForm">
                @csrf
                
                {{-- Token dikirim secara tersembunyi (diberikan dari controller ke view ini) --}}
                <input type="hidden" name="token" value="{{ $token ?? '' }}">

                <div class="mb-3">
                    <label class="auth-label" for="email">Alamat Email</label>
                    <div class="auth-input-group">
                        <div class="ig-icon"><i class="fa fa-envelope"></i></div>
                        <input type="email" name="email" id="email" value="{{ request()->email ?? old('email') }}" readonly required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="auth-label" for="password">Kata Sandi Baru</label>
                    <div class="auth-input-group">
                        <div class="ig-icon"><i class="fa fa-lock"></i></div>
                        <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" required autofocus autocomplete="new-password">
                        <div class="ig-toggle" onclick="togglePw('password', 'eyeIcon1')"><i class="fa fa-eye" id="eyeIcon1"></i></div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="auth-label" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                    <div class="auth-input-group">
                        <div class="ig-icon"><i class="fa fa-check-double"></i></div>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi kata sandi baru" required autocomplete="new-password">
                        <div class="ig-toggle" onclick="togglePw('password_confirmation', 'eyeIcon2')"><i class="fa fa-eye" id="eyeIcon2"></i></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold text-white mt-2" id="submitBtn">
                    <span id="btnLabel"><i class="fa fa-save me-2"></i>Simpan Kata Sandi Baru</span>
                    <span class="spinner-border spinner-border-sm d-none" id="btnSpinner" role="status"></span>
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    function togglePw(inputId, iconId) {
        const pw = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !show);
        icon.classList.toggle('fa-eye-slash', show);
    }

    document.getElementById('resetForm').addEventListener('submit', function () {
        document.getElementById('btnLabel').classList.add('d-none');
        document.getElementById('btnSpinner').classList.remove('d-none');
        document.getElementById('submitBtn').disabled = true;
    });
</script>
</body>
</html>