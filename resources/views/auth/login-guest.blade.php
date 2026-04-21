<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login – {{ config('hotel.name', 'Hotelier') }}</title>
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
        .auth-left { width:300px; flex-shrink:0; background:#1a1f2e; display:flex; flex-direction:column; padding:36px 30px; position:relative; overflow:hidden; }
        .auth-left::before { content:''; position:absolute; top:-80px; left:-80px; width:240px; height:240px; border-radius:50%; background:rgba(243,156,18,.07); pointer-events:none; }
        .auth-left::after  { content:''; position:absolute; bottom:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(243,156,18,.05); pointer-events:none; }
        .auth-brand { display:flex; align-items:center; gap:10px; margin-bottom:44px; text-decoration:none; }
        .auth-brand-icon { width:36px; height:36px; background:#f39c12; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .auth-brand-icon i { font-size:16px; color:#fff; }
        .auth-brand-name { font-size:18px; font-weight:700; color:#fff; letter-spacing:-.3px; }
        .auth-illus { flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; }
        .auth-hotel-icon { width:80px; height:80px; border-radius:50%; background:rgba(243,156,18,.1); border:1px solid rgba(243,156,18,.2); display:flex; align-items:center; justify-content:center; margin-bottom:20px; }
        .auth-hotel-icon i { font-size:32px; color:#f39c12; opacity:.85; }
        .auth-headline { font-size:18px; font-weight:700; color:#fff; line-height:1.4; margin-bottom:10px; }
        .auth-desc { font-size:12.5px; color:rgba(255,255,255,.4); line-height:1.7; max-width:200px; }
        .auth-features { margin-top:28px; list-style:none; padding:0; width:100%; }
        .auth-features li { display:flex; align-items:center; gap:9px; font-size:12px; color:rgba(255,255,255,.45); padding:5px 0; }
        .auth-features li::before { content:''; width:5px; height:5px; border-radius:50%; background:#f39c12; opacity:.75; flex-shrink:0; }
        .auth-right { flex:1; background:#fff; padding:40px; display:flex; flex-direction:column; justify-content:center; }
        .auth-back { display:inline-flex; align-items:center; gap:6px; font-size:12.5px; color:#6c757d; text-decoration:none; margin-bottom:28px; transition:color .15s; }
        .auth-back:hover { color:#f39c12; }
        .auth-section-tag { font-size:10.5px; font-weight:600; color:#f39c12; text-transform:uppercase; letter-spacing:1.2px; margin-bottom:6px; }
        .auth-title { font-size:26px; font-weight:700; color:#1a1f2e; margin-bottom:4px; }
        .auth-subtitle { font-size:13px; color:#6c757d; margin-bottom:28px; }
        .auth-label { display:block; font-size:11px; font-weight:600; color:#344767; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
        .auth-input-group { display:flex; align-items:center; border:1px solid #e0e5ec; border-radius:8px; background:#f8f9fa; overflow:hidden; transition:border-color .2s,box-shadow .2s; }
        .auth-input-group:focus-within { border-color:#f39c12; background:#fff; box-shadow:0 0 0 3px rgba(243,156,18,.12); }
        .auth-input-group .ig-icon { width:42px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .auth-input-group .ig-icon i { font-size:13px; color:#f39c12; opacity:.8; }
        .auth-input-group input { flex:1; border:none; background:transparent; font-family:'Heebo',sans-serif; font-size:13.5px; color:#344767; padding:11px 10px 11px 0; outline:none; }
        .auth-input-group input::placeholder { color:#b2bec3; }
        .auth-input-group .ig-toggle { width:40px; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; }
        .auth-input-group .ig-toggle i { font-size:13px; color:#adb5bd; transition:color .15s; }
        .auth-input-group .ig-toggle:hover i { color:#f39c12; }
        .auth-divider { display:flex; align-items:center; gap:12px; margin:12px 0 16px; }
        .auth-divider::before,.auth-divider::after { content:''; flex:1; height:1px; background:#e9ecef; }
        .auth-divider span { font-size:11.5px; color:#adb5bd; white-space:nowrap; }
        @media(max-width:640px){ .auth-left{display:none} .auth-right{padding:28px 20px} }
    </style>
</head>
<body>

<div class="bg-dark py-2 px-4 d-none d-lg-flex align-items-center justify-content-between">
    <small class="text-white-50"><i class="fa fa-phone-alt text-warning me-2" style="font-size:11px"></i>{{ config('hotel.phone', '+012 345 6789') }}</small>
    <small class="text-white-50"><i class="far fa-clock text-warning me-2" style="font-size:11px"></i>{{ config('hotel.hours', 'Mon – Fri : 09.00 AM – 09.00 PM') }}</small>
</div>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-left">
            <a href="{{ route('home') }}" class="auth-brand">
                <div class="auth-brand-icon"><i class="fa fa-hotel"></i></div>
                <div class="auth-brand-name">{{ config('hotel.name', 'Hotelier') }}</div>
            </a>
            <div class="auth-illus">
                <div class="auth-hotel-icon"><i class="fa fa-hotel"></i></div>
                <div class="auth-headline">Welcome to<br>{{ config('hotel.name', 'Hotelier') }}</div>
                <div class="auth-desc">Your gateway to a world-class luxury hotel experience.</div>
                <ul class="auth-features">
                    <li>Easy online booking</li>
                    <li>Manage your reservations</li>
                    <li>Exclusive member rates</li>
                    <li>24/7 customer support</li>
                </ul>
            </div>
        </div>

        <div class="auth-right">
            <a href="{{ route('home') }}" class="auth-back"><i class="fa fa-arrow-left"></i> Back to Home</a>

            <div class="auth-section-tag">Member Access</div>
            <div class="auth-title">Sign In</div>
            <div class="auth-subtitle">Enter your credentials to access your account.</div>

            @if(session('status'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-3 py-2">
                    <i class="fa fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 py-2">
                    <i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('guest.login.submit') }}" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label class="auth-label" for="email">Email Address</label>
                    <div class="auth-input-group">
                        <div class="ig-icon"><i class="fa fa-envelope"></i></div>
                        <input type="email" name="email" id="email" placeholder="your@email.com" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="auth-label" for="password">Password</label>
                    <div class="auth-input-group">
                        <div class="ig-icon"><i class="fa fa-lock"></i></div>
                        <input type="password" name="password" id="password" placeholder="••••••••" required autocomplete="current-password">
                        <div class="ig-toggle" id="togglePw"><i class="fa fa-eye" id="eyeIcon"></i></div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">Remember me</label>
                    </div>
                    <a href="{{ route('guest.forgot') }}" class="small text-decoration-none" style="color:#f39c12; font-weight: 600;">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold" id="loginBtn">
                    <span id="loginLabel"><i class="fa fa-sign-in-alt me-2"></i>Sign In</span>
                    <span class="spinner-border spinner-border-sm d-none" id="loginSpinner" role="status"></span>
                </button>
            </form>

            <div class="auth-divider"><span>don't have an account?</span></div>

            <a href="{{ route('guest.register') }}" class="btn btn-outline-secondary w-100 py-2" style="font-size:13.5px">
                <i class="fa fa-user-plus me-2"></i>Create a New Account
            </a>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('togglePw').addEventListener('click', function () {
        const pw = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !show);
        icon.classList.toggle('fa-eye-slash', show);
    });

    document.getElementById('loginForm').addEventListener('submit', function () {
        document.getElementById('loginLabel').classList.add('d-none');
        document.getElementById('loginSpinner').classList.remove('d-none');
        document.getElementById('loginBtn').disabled = true;
    });
</script>
</body>
</html>