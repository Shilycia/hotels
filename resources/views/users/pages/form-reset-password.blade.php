<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create New Password – {{ config('hotel.name', 'Hotelier') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        /* (Copy paste block CSS dari file forgot-password.blade.php kamu ke sini) */
        body { font-family:'Heebo',sans-serif; background:#f8f9fa; min-height:100vh; display:flex; flex-direction:column; }
        .auth-wrapper { flex:1; display:flex; align-items:center; justify-content:center; padding:40px 16px; }
        .auth-card { display:flex; width:100%; max-width:860px; min-height:540px; border-radius:12px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.1); }
        .auth-left { width:300px; flex-shrink:0; background:#1a1f2e; display:flex; flex-direction:column; padding:36px 30px; position:relative; overflow:hidden; }
        .auth-brand { display:flex; align-items:center; gap:10px; margin-bottom:44px; text-decoration:none; }
        .auth-brand-icon { width:36px; height:36px; background:#f39c12; border-radius:8px; display:flex; align-items:center; justify-content:center; }
        .auth-brand-icon i { font-size:16px; color:#fff; }
        .auth-brand-name { font-size:18px; font-weight:700; color:#fff; }
        .auth-illus { flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; }
        .auth-center-icon { width:80px; height:80px; border-radius:50%; background:rgba(243,156,18,.1); border:1px solid rgba(243,156,18,.2); display:flex; align-items:center; justify-content:center; margin-bottom:20px; }
        .auth-center-icon i { font-size:30px; color:#f39c12; }
        .auth-headline { font-size:18px; font-weight:700; color:#fff; line-height:1.4; margin-bottom:10px; }
        .auth-desc { font-size:12.5px; color:rgba(255,255,255,.4); line-height:1.7; max-width:200px; }
        .auth-right { flex:1; background:#fff; padding:40px; display:flex; flex-direction:column; justify-content:center; }
        .auth-section-tag { font-size:10.5px; font-weight:600; color:#f39c12; text-transform:uppercase; letter-spacing:1.2px; margin-bottom:6px; }
        .auth-title { font-size:26px; font-weight:700; color:#1a1f2e; margin-bottom:4px; }
        .auth-subtitle { font-size:13px; color:#6c757d; margin-bottom:28px; line-height:1.6; }
        .auth-label { display:block; font-size:11px; font-weight:600; color:#344767; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
        .auth-input-group { display:flex; align-items:center; border:1px solid #e0e5ec; border-radius:8px; background:#f8f9fa; overflow:hidden; transition:border-color .2s; }
        .auth-input-group:focus-within { border-color:#f39c12; background:#fff; box-shadow:0 0 0 3px rgba(243,156,18,.12); }
        .auth-input-group .ig-icon { width:42px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .auth-input-group .ig-icon i { font-size:13px; color:#f39c12; }
        .auth-input-group input { flex:1; border:none; background:transparent; font-size:13.5px; padding:11px 10px 11px 0; outline:none; }
        .ig-toggle { width:40px; display:flex; align-items:center; justify-content:center; cursor:pointer; }
        @media(max-width:640px){ .auth-left{display:none} .auth-right{padding:28px 20px} }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-left">
            <a href="{{ route('home') }}" class="auth-brand">
                <div class="auth-brand-icon"><i class="fa fa-hotel"></i></div>
                <div class="auth-brand-name">{{ config('hotel.name', 'Hotelier') }}</div>
            </a>
            <div class="auth-illus">
                <div class="auth-center-icon"><i class="fa fa-lock"></i></div>
                <div class="auth-headline">Secure Your<br>Account</div>
                <div class="auth-desc">Choose a strong, unique password to protect your data.</div>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-section-tag">Security Upgrade</div>
            <div class="auth-title">Create New Password</div>
            <div class="auth-subtitle">Please enter your new password below to regain access.</div>

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 py-2">
                    <i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('guest.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label class="auth-label">Email Address</label>
                    <div class="auth-input-group" style="background:#e9ecef; border-color:#e9ecef;">
                        <div class="ig-icon"><i class="fa fa-envelope" style="color:#adb5bd"></i></div>
                        <input type="email" name="email" value="{{ $email }}" readonly style="color:#6c757d; cursor:not-allowed;">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="auth-label" for="password">New Password</label>
                    <div class="auth-input-group">
                        <div class="ig-icon"><i class="fa fa-key"></i></div>
                        <input type="password" name="password" id="password" placeholder="Min. 8 characters" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="auth-label" for="password_confirmation">Confirm Password</label>
                    <div class="auth-input-group">
                        <div class="ig-icon"><i class="fa fa-check-double"></i></div>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Re-enter password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold">
                    <i class="fa fa-save me-2"></i>Save New Password
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>