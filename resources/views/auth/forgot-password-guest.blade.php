<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password – {{ config('hotel.name', 'Hotelier') }}</title>
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

        /* Left panel */
        .auth-left { width:300px; flex-shrink:0; background:#1a1f2e; display:flex; flex-direction:column; padding:36px 30px; position:relative; overflow:hidden; }
        .auth-left::before { content:''; position:absolute; top:-80px; left:-80px; width:240px; height:240px; border-radius:50%; background:rgba(243,156,18,.07); pointer-events:none; }
        .auth-left::after  { content:''; position:absolute; bottom:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(243,156,18,.05); pointer-events:none; }
        .auth-brand { display:flex; align-items:center; gap:10px; margin-bottom:44px; text-decoration:none; }
        .auth-brand-icon { width:36px; height:36px; background:#f39c12; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .auth-brand-icon i { font-size:16px; color:#fff; }
        .auth-brand-name { font-size:18px; font-weight:700; color:#fff; letter-spacing:-.3px; }
        .auth-illus { flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; }
        .auth-center-icon { width:80px; height:80px; border-radius:50%; background:rgba(243,156,18,.1); border:1px solid rgba(243,156,18,.2); display:flex; align-items:center; justify-content:center; margin-bottom:20px; animation:pulse 3s ease-in-out infinite; }
        .auth-center-icon i { font-size:30px; color:#f39c12; opacity:.85; }
        @keyframes pulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.05);opacity:.8} }
        .auth-headline { font-size:18px; font-weight:700; color:#fff; line-height:1.4; margin-bottom:10px; }
        .auth-desc { font-size:12.5px; color:rgba(255,255,255,.4); line-height:1.7; max-width:200px; }
        .auth-steps { margin-top:28px; width:100%; list-style:none; padding:0; }
        .auth-steps li { display:flex; align-items:flex-start; gap:10px; padding:9px 0; border-bottom:1px solid rgba(255,255,255,.05); font-size:12px; color:rgba(255,255,255,.4); line-height:1.5; }
        .auth-steps li:last-child { border-bottom:none; }
        .auth-steps .step-num { width:20px; height:20px; border-radius:50%; background:rgba(243,156,18,.15); border:1px solid rgba(243,156,18,.3); display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; color:#f39c12; flex-shrink:0; margin-top:1px; }
        .auth-steps strong { display:block; color:rgba(255,255,255,.7); font-weight:600; margin-bottom:1px; }

        /* Right panel */
        .auth-right { flex:1; background:#fff; padding:40px; display:flex; flex-direction:column; justify-content:center; }
        .auth-back { display:inline-flex; align-items:center; gap:6px; font-size:12.5px; color:#6c757d; text-decoration:none; margin-bottom:28px; transition:color .15s; }
        .auth-back:hover { color:#f39c12; }
        .auth-section-tag { font-size:10.5px; font-weight:600; color:#f39c12; text-transform:uppercase; letter-spacing:1.2px; margin-bottom:6px; }
        .auth-title { font-size:26px; font-weight:700; color:#1a1f2e; margin-bottom:4px; }
        .auth-subtitle { font-size:13px; color:#6c757d; margin-bottom:28px; line-height:1.6; }

        /* Input */
        .auth-label { display:block; font-size:11px; font-weight:600; color:#344767; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
        .auth-input-group { display:flex; align-items:center; border:1px solid #e0e5ec; border-radius:8px; background:#f8f9fa; overflow:hidden; transition:border-color .2s,box-shadow .2s; }
        .auth-input-group:focus-within { border-color:#f39c12; background:#fff; box-shadow:0 0 0 3px rgba(243,156,18,.12); }
        .auth-input-group .ig-icon { width:42px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .auth-input-group .ig-icon i { font-size:13px; color:#f39c12; opacity:.8; }
        .auth-input-group input { flex:1; border:none; background:transparent; font-family:'Heebo',sans-serif; font-size:13.5px; color:#344767; padding:11px 10px 11px 0; outline:none; }
        .auth-input-group input::placeholder { color:#b2bec3; }
        .auth-hint { font-size:11.5px; color:#adb5bd; margin-top:5px; }

        /* Divider */
        .auth-divider { display:flex; align-items:center; gap:12px; margin:12px 0 16px; }
        .auth-divider::before,.auth-divider::after { content:''; flex:1; height:1px; background:#e9ecef; }
        .auth-divider span { font-size:11.5px; color:#adb5bd; white-space:nowrap; }

        /* Success state */
        .success-block { text-align:center; padding:8px 0; }
        .success-icon { width:72px; height:72px; border-radius:50%; background:#d4edda; border:1px solid #b8dac6; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
        .success-icon i { font-size:28px; color:#27ae60; }
        .success-title { font-size:22px; font-weight:700; color:#1a1f2e; margin-bottom:8px; }
        .success-desc { font-size:13px; color:#6c757d; line-height:1.6; margin-bottom:24px; }

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

        {{-- Left panel --}}
        <div class="auth-left">
            <a href="{{ route('home') }}" class="auth-brand">
                <div class="auth-brand-icon"><i class="fa fa-hotel"></i></div>
                <div class="auth-brand-name">{{ config('hotel.name', 'Hotelier') }}</div>
            </a>
            <div class="auth-illus">
                <div class="auth-center-icon"><i class="fa fa-key"></i></div>
                <div class="auth-headline">Recover Your<br>Access</div>
                <div class="auth-desc">Follow these simple steps to reset your account password.</div>
                <ul class="auth-steps">
                    <li>
                        <div class="step-num">1</div>
                        <div><strong>Enter your email</strong>Type the address registered in the system.</div>
                    </li>
                    <li>
                        <div class="step-num">2</div>
                        <div><strong>Check your inbox</strong>We'll send a reset link to that email.</div>
                    </li>
                    <li>
                        <div class="step-num">3</div>
                        <div><strong>Set a new password</strong>Click the link and choose a new password.</div>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Right panel --}}
        <div class="auth-right">
            <a href="{{ route('guest.login') }}" class="auth-back"><i class="fa fa-arrow-left"></i> Back to Login</a>

            @if(session('status'))
            {{-- Success state --}}
            <div class="success-block">
                <div class="success-icon"><i class="fa fa-envelope-open-text"></i></div>
                <div class="success-title">Email Sent!</div>
                <div class="success-desc">
                    A password reset link has been sent to your email address.
                    Please check your inbox or spam folder.
                </div>
                <a href="{{ route('guest.login') }}" class="btn btn-outline-secondary w-100 py-2" style="font-size:13.5px">
                    <i class="fa fa-arrow-left me-2"></i>Back to Login
                </a>
            </div>
            @else
            {{-- Form state --}}
            <div class="auth-section-tag">Password Recovery</div>
            <div class="auth-title">Forgot Password?</div>
            <div class="auth-subtitle">No worries. Enter your email and we'll send you a reset link.</div>

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 py-2">
                    <i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('guest.password.email') }}" id="forgotForm">
                @csrf
                <div class="mb-4">
                    <label class="auth-label" for="email">Email Address</label>
                    <div class="auth-input-group">
                        <div class="ig-icon"><i class="fa fa-envelope"></i></div>
                        <input type="email" name="email" id="email"
                            placeholder="your@email.com"
                            value="{{ old('email') }}"
                            required autocomplete="email">
                    </div>
                    <div class="auth-hint">Use the email address registered to your account.</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold mb-3" id="submitBtn">
                    <span id="submitLabel"><i class="fa fa-paper-plane me-2"></i>Send Reset Link</span>
                    <span class="spinner-border spinner-border-sm d-none" id="submitSpinner" role="status"></span>
                </button>
            </form>

            <div class="auth-divider"><span>remembered your password?</span></div>

            <a href="{{ route('guest.login') }}" class="btn btn-outline-secondary w-100 py-2" style="font-size:13.5px">
                <i class="fa fa-sign-in-alt me-2"></i>Sign In Instead
            </a>
            @endif
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const form = document.getElementById('forgotForm');
    if (form) {
        form.addEventListener('submit', function () {
            document.getElementById('submitLabel').classList.add('d-none');
            document.getElementById('submitSpinner').classList.remove('d-none');
            document.getElementById('submitBtn').disabled = true;
        });
    }
</script>
</body>
</html>