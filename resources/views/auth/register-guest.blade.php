<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register – {{ config('hotel.name', 'Hotelier') }}</title>
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
        .auth-card { display:flex; width:100%; max-width:900px; border-radius:12px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.1); }

        /* Left panel */
        .auth-left { width:300px; flex-shrink:0; background:#1a1f2e; display:flex; flex-direction:column; padding:36px 30px; position:relative; overflow:hidden; }
        .auth-left::before { content:''; position:absolute; top:-80px; left:-80px; width:240px; height:240px; border-radius:50%; background:rgba(243,156,18,.07); pointer-events:none; }
        .auth-left::after  { content:''; position:absolute; bottom:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(243,156,18,.05); pointer-events:none; }
        .auth-brand { display:flex; align-items:center; gap:10px; margin-bottom:36px; text-decoration:none; }
        .auth-brand-icon { width:36px; height:36px; background:#f39c12; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .auth-brand-icon i { font-size:16px; color:#fff; }
        .auth-brand-name { font-size:18px; font-weight:700; color:#fff; letter-spacing:-.3px; }
        .auth-illus { flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; }
        .auth-center-icon { width:72px; height:72px; border-radius:50%; background:rgba(243,156,18,.1); border:1px solid rgba(243,156,18,.2); display:flex; align-items:center; justify-content:center; margin-bottom:18px; }
        .auth-center-icon i { font-size:28px; color:#f39c12; opacity:.85; }
        .auth-headline { font-size:17px; font-weight:700; color:#fff; line-height:1.4; margin-bottom:10px; }
        .auth-desc { font-size:12px; color:rgba(255,255,255,.4); line-height:1.7; max-width:200px; }
        .auth-perks { margin-top:24px; width:100%; list-style:none; padding:0; }
        .auth-perks li { display:flex; align-items:center; gap:9px; font-size:12px; color:rgba(255,255,255,.45); padding:5px 0; }
        .auth-perks li::before { content:''; width:5px; height:5px; border-radius:50%; background:#f39c12; opacity:.75; flex-shrink:0; }

        /* Right panel */
        .auth-right { flex:1; background:#fff; padding:36px 40px; display:flex; flex-direction:column; justify-content:center; overflow-y:auto; }
        .auth-back { display:inline-flex; align-items:center; gap:6px; font-size:12.5px; color:#6c757d; text-decoration:none; margin-bottom:22px; transition:color .15s; }
        .auth-back:hover { color:#f39c12; }
        .auth-section-tag { font-size:10.5px; font-weight:600; color:#f39c12; text-transform:uppercase; letter-spacing:1.2px; margin-bottom:5px; }
        .auth-title { font-size:24px; font-weight:700; color:#1a1f2e; margin-bottom:3px; }
        .auth-subtitle { font-size:13px; color:#6c757d; margin-bottom:22px; }

        /* Input */
        .auth-label { display:block; font-size:11px; font-weight:600; color:#344767; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
        .auth-input-group { display:flex; align-items:center; border:1px solid #e0e5ec; border-radius:8px; background:#f8f9fa; overflow:hidden; transition:border-color .2s,box-shadow .2s; }
        .auth-input-group:focus-within { border-color:#f39c12; background:#fff; box-shadow:0 0 0 3px rgba(243,156,18,.12); }
        .auth-input-group .ig-icon { width:42px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .auth-input-group .ig-icon i { font-size:13px; color:#f39c12; opacity:.8; }
        .auth-input-group input { flex:1; border:none; background:transparent; font-family:'Heebo',sans-serif; font-size:13.5px; color:#344767; padding:10px 10px 10px 0; outline:none; }
        .auth-input-group input::placeholder { color:#b2bec3; }
        .auth-input-group .ig-toggle { width:40px; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; }
        .auth-input-group .ig-toggle i { font-size:13px; color:#adb5bd; transition:color .15s; }
        .auth-input-group .ig-toggle:hover i { color:#f39c12; }
        .field-error { font-size:11.5px; color:#dc3545; margin-top:4px; }

        /* Password strength */
        .strength-wrap { margin-top:6px; }
        .strength-bar-bg { height:4px; border-radius:2px; background:#e9ecef; overflow:hidden; }
        .strength-bar { height:100%; border-radius:2px; width:0; transition:width .3s,background .3s; }
        .strength-label { font-size:11px; color:#adb5bd; margin-top:3px; }

        /* Divider */
        .auth-divider { display:flex; align-items:center; gap:12px; margin:10px 0 14px; }
        .auth-divider::before,.auth-divider::after { content:''; flex:1; height:1px; background:#e9ecef; }
        .auth-divider span { font-size:11.5px; color:#adb5bd; white-space:nowrap; }

        /* Two column grid */
        .form-row-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }

        @media(max-width:640px){ .auth-left{display:none} .auth-right{padding:28px 20px} .form-row-2{grid-template-columns:1fr} }
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
                <div class="auth-center-icon"><i class="fa fa-user-plus"></i></div>
                <div class="auth-headline">Join Hotelier<br>Today</div>
                <div class="auth-desc">Create your account and enjoy exclusive benefits as a member.</div>
                <ul class="auth-perks">
                    <li>Exclusive member-only rates</li>
                    <li>Fast & easy booking</li>
                    <li>Manage all reservations</li>
                    <li>Priority customer support</li>
                    <li>Special offers & promotions</li>
                </ul>
            </div>
        </div>

        {{-- Right panel --}}
        <div class="auth-right">
            <a href="{{ route('home') }}" class="auth-back"><i class="fa fa-arrow-left"></i> Back to Home</a>

            <div class="auth-section-tag">New Account</div>
            <div class="auth-title">Create Account</div>
            <div class="auth-subtitle">Register to enjoy exclusive rates and easy booking management.</div>

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-start gap-2 mb-3 py-2">
                    <i class="fa fa-exclamation-circle mt-1"></i>
                    <div>
                        <strong>Please fix the following:</strong>
                        <ul class="mb-0 mt-1 ps-3 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('guest.register.submit') }}" id="registerForm">
                @csrf

                {{-- Name row --}}
                <div class="form-row-2 mb-3">
                    <div>
                        <label class="auth-label" for="name">Full Name</label>
                        <div class="auth-input-group">
                            <div class="ig-icon"><i class="fa fa-user"></i></div>
                            <input type="text" name="name" id="name"
                                placeholder="Your full name"
                                value="{{ old('name') }}"
                                required autocomplete="name">
                        </div>
                        @error('name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="auth-label" for="phone">Phone <span class="text-muted fw-normal" style="text-transform:none;letter-spacing:0">(optional)</span></label>
                        <div class="auth-input-group">
                            <div class="ig-icon"><i class="fa fa-phone"></i></div>
                            <input type="tel" name="phone" id="phone"
                                placeholder="+62 812 xxxx"
                                value="{{ old('phone') }}"
                                autocomplete="tel">
                        </div>
                        @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="auth-label" for="email">Email Address</label>
                    <div class="auth-input-group">
                        <div class="ig-icon"><i class="fa fa-envelope"></i></div>
                        <input type="email" name="email" id="email"
                            placeholder="your@email.com"
                            value="{{ old('email') }}"
                            required autocomplete="email">
                    </div>
                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                {{-- Password row --}}
                <div class="form-row-2 mb-3">
                    <div>
                        <label class="auth-label" for="password">Password</label>
                        <div class="auth-input-group">
                            <div class="ig-icon"><i class="fa fa-lock"></i></div>
                            <input type="password" name="password" id="password"
                                placeholder="Min. 8 characters"
                                required autocomplete="new-password">
                            <div class="ig-toggle" id="togglePw"><i class="fa fa-eye" id="eyeIcon"></i></div>
                        </div>
                        <div class="strength-wrap" id="strengthWrap" style="display:none">
                            <div class="strength-bar-bg"><div class="strength-bar" id="strengthBar"></div></div>
                            <div class="strength-label" id="strengthLabel"></div>
                        </div>
                        @error('password')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="auth-label" for="password_confirmation">Confirm Password</label>
                        <div class="auth-input-group">
                            <div class="ig-icon"><i class="fa fa-lock"></i></div>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="Re-enter password"
                                required autocomplete="new-password">
                            <div class="ig-toggle" id="matchIcon"><i class="fa fa-minus text-muted" id="matchIconEl"></i></div>
                        </div>
                        <div class="field-error d-none" id="matchError">Passwords do not match.</div>
                    </div>
                </div>

                {{-- Terms --}}
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label small text-muted" for="terms">
                        I agree to the <a href="#" class="text-warning text-decoration-none">Terms & Conditions</a>
                        and <a href="#" class="text-warning text-decoration-none">Privacy Policy</a>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold" id="registerBtn">
                    <span id="registerLabel"><i class="fa fa-user-plus me-2"></i>Create My Account</span>
                    <span class="spinner-border spinner-border-sm d-none" id="registerSpinner" role="status"></span>
                </button>
            </form>

            <div class="auth-divider"><span>already have an account?</span></div>

            <a href="{{ route('guest.login') }}" class="btn btn-outline-secondary w-100 py-2" style="font-size:13.5px">
                <i class="fa fa-sign-in-alt me-2"></i>Sign In Instead
            </a>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle password visibility
    document.getElementById('togglePw').addEventListener('click', function () {
        const pw = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !show);
        icon.classList.toggle('fa-eye-slash', show);
    });

    // Password strength
    const pwInput = document.getElementById('password');
    pwInput.addEventListener('input', function () {
        const v = this.value;
        const wrap = document.getElementById('strengthWrap');
        const bar  = document.getElementById('strengthBar');
        const lbl  = document.getElementById('strengthLabel');
        wrap.style.display = v.length ? 'block' : 'none';
        let score = 0;
        if (v.length >= 8)           score++;
        if (/[A-Z]/.test(v))         score++;
        if (/[0-9]/.test(v))         score++;
        if (/[^A-Za-z0-9]/.test(v))  score++;
        const levels = [
            { w:'25%', bg:'#dc3545', t:'Weak' },
            { w:'50%', bg:'#fd7e14', t:'Fair' },
            { w:'75%', bg:'#ffc107', t:'Good' },
            { w:'100%',bg:'#28a745', t:'Strong' },
        ];
        const lvl = levels[Math.max(score - 1, 0)];
        bar.style.width      = lvl.w;
        bar.style.background = lvl.bg;
        lbl.textContent      = 'Strength: ' + lvl.t;
        checkMatch();
    });

    // Password match
    const pwConfirm  = document.getElementById('password_confirmation');
    const matchError = document.getElementById('matchError');
    const matchEl    = document.getElementById('matchIconEl');
    function checkMatch() {
        const match = pwInput.value === pwConfirm.value && pwConfirm.value !== '';
        const empty = pwConfirm.value === '';
        matchError.classList.toggle('d-none', match || empty);
        matchEl.className = empty
            ? 'fa fa-minus text-muted'
            : match ? 'fa fa-check text-success' : 'fa fa-times text-danger';
    }
    pwConfirm.addEventListener('input', checkMatch);

    // Submit
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        if (pwInput.value !== pwConfirm.value) { e.preventDefault(); pwConfirm.focus(); return; }
        document.getElementById('registerLabel').classList.add('d-none');
        document.getElementById('registerSpinner').classList.remove('d-none');
        document.getElementById('registerBtn').disabled = true;
    });
</script>
</body>
</html>