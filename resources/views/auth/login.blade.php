<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login Admin – Hotel Neo</title>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --sand: #f7f3ee;
            --sand2: #ede8e0;
            --sand3: #e4ddd3;
            --stone: #c8bfb0;
            --bark: #8b7355;
            --bark-soft: #f4ede4;
            --moss: #4a7c59;
            --moss-soft: #edf4ef;
            --clay: #c07850;
            --clay-soft: #fdf0e8;
            --ink: #2c2420;
            --ink2: #6b5e54;
            --ink3: #9e9088;
            --sidebar-bg: #2c2820;
        }

        *, ::before, ::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { height: 100%; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--sand);
            min-height: 100vh;
            display: flex;
            color: var(--ink);
        }

        /* ── Left panel ── */
        .panel-left {
            width: 420px;
            flex-shrink: 0;
            background: var(--sidebar-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 48px 44px;
            position: relative;
            overflow: hidden;
        }

        .panel-left::before {
            content: '';
            position: absolute;
            top: -100px; left: -100px;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: rgba(200,169,110,0.06);
            pointer-events: none;
        }

        .panel-left::after {
            content: '';
            position: absolute;
            bottom: -80px; right: -80px;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: rgba(74,124,89,0.07);
            pointer-events: none;
        }

        .panel-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 56px;
        }

        .panel-brand-icon {
            width: 36px; height: 36px;
            border-radius: 9px;
            background: #c8a96e;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .panel-brand-icon i { font-size: 14px; color: #2c2820; }

        .panel-brand-name {
            font-family: 'Lora', serif;
            font-size: 17px;
            color: #f0ebe3;
            font-style: italic;
        }

        .panel-brand-sub {
            font-size: 10px;
            color: rgba(240,235,227,0.3);
            margin-top: 2px;
            letter-spacing: 0.9px;
            text-transform: uppercase;
        }

        /* Hotel illustration */
        .panel-illustration {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .hotel-svg {
            width: 200px;
            margin-bottom: 32px;
            animation: float 5s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }

        .panel-headline {
            font-family: 'Lora', serif;
            font-size: 22px;
            color: #f0ebe3;
            font-weight: 400;
            font-style: italic;
            text-align: center;
            line-height: 1.4;
            margin-bottom: 10px;
        }

        .panel-desc {
            font-size: 12.5px;
            color: rgba(240,235,227,0.45);
            text-align: center;
            line-height: 1.7;
            max-width: 260px;
        }

        /* ── Right panel ── */
        .panel-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
            background: var(--sand);
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            animation: fadeUp .35s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-eyebrow {
            display: inline-block;
            font-size: 10px;
            font-weight: 500;
            color: var(--bark);
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding: 3px 10px;
            background: var(--bark-soft);
            border-radius: 50px;
        }

        .card-title {
            font-family: 'Lora', serif;
            font-size: 28px;
            color: var(--ink);
            font-weight: 400;
            font-style: italic;
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .card-subtitle {
            font-size: 13px;
            color: var(--ink3);
            margin-bottom: 32px;
            line-height: 1.6;
        }

        /* Alert */
        .alert {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            margin-bottom: 16px;
        }

        .alert-error {
            background: #fceee9;
            color: #8a3a24;
            border: 1px solid #f5c4b0;
        }

        .alert-success {
            background: var(--moss-soft);
            color: #2e6644;
            border: 1px solid #b7d8c2;
        }

        /* Form */
        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 500;
            color: var(--ink2);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 13px; top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: var(--stone);
            pointer-events: none;
            transition: color .2s;
        }

        .toggle-pw {
            position: absolute;
            right: 13px; top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: var(--stone);
            cursor: pointer;
            transition: color .2s;
        }

        .toggle-pw:hover { color: var(--bark); }

        .form-control {
            width: 100%;
            padding: 10px 13px 10px 36px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            color: var(--ink);
            background: #ffffff;
            border: 1px solid var(--sand3);
            border-radius: 8px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            border-color: var(--bark);
            box-shadow: 0 0 0 3px var(--bark-soft);
        }

        .form-control:focus + .input-icon,
        .input-wrap:focus-within .input-icon { color: var(--bark); }

        .form-control::placeholder { color: var(--stone); }

        .form-control.has-toggle { padding-right: 36px; }

        /* Extras row */
        .form-extras {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
        }

        .remember-label input[type=checkbox] {
            width: 15px; height: 15px;
            accent-color: var(--bark);
            cursor: pointer;
        }

        .remember-label span {
            font-size: 12.5px;
            color: var(--ink3);
        }

        .forgot-link {
            font-size: 12.5px;
            color: var(--bark);
            font-weight: 500;
            text-decoration: none;
            transition: opacity .18s;
        }

        .forgot-link:hover { opacity: 0.7; }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 11px 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            color: #fff;
            background: var(--bark);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background .18s, transform .14s;
            position: relative;
            overflow: hidden;
        }

        .btn-submit:hover  { background: #7a6448; }
        .btn-submit:active { transform: scale(.98); }
        .btn-submit:disabled { opacity: .65; cursor: not-allowed; }

        .btn-spinner {
            width: 14px; height: 14px;
            border: 2px solid rgba(255,255,255,.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            display: none;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .btn-submit.loading .btn-spinner { display: block; }
        .btn-submit.loading .btn-label  { display: none; }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0 16px;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--sand2);
        }

        .divider span {
            font-size: 11px;
            color: var(--stone);
            white-space: nowrap;
            letter-spacing: 0.3px;
        }

        .card-footer {
            text-align: center;
            font-size: 12px;
            color: var(--ink3);
        }

        .card-footer a {
            color: var(--bark);
            font-weight: 500;
            text-decoration: none;
        }

        .card-footer a:hover { text-decoration: underline; }

        /* Responsive */
        @media (max-width: 768px) {
            .panel-left { display: none; }
            .panel-right { padding: 32px 20px; }
        }
    </style>
</head>
<body>

    <!-- Left panel -->
    <div class="panel-left">
        <div class="panel-brand">
            <div class="panel-brand-icon">
                <i class="fas fa-hotel"></i>
            </div>
            <div>
                <div class="panel-brand-name">Hotel Neo</div>
                <div class="panel-brand-sub">Admin</div>
            </div>
        </div>

        <div class="panel-illustration">
            <svg class="hotel-svg" viewBox="0 0 200 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Building body -->
                <rect x="30" y="60" width="140" height="115" rx="6" fill="rgba(200,169,110,0.18)"/>
                <!-- Roof band -->
                <rect x="22" y="46" width="156" height="16" rx="5" fill="rgba(200,169,110,0.28)"/>
                <!-- Flagpole -->
                <line x1="100" y1="12" x2="100" y2="46" stroke="rgba(200,169,110,0.5)" stroke-width="2"/>
                <polygon points="100,12 120,20 100,28" fill="rgba(200,169,110,0.6)"/>
                <!-- Windows row 1 -->
                <rect x="44" y="76" width="24" height="18" rx="3" fill="rgba(240,235,227,0.18)"/>
                <rect x="88" y="76" width="24" height="18" rx="3" fill="rgba(240,235,227,0.26)"/>
                <rect x="132" y="76" width="24" height="18" rx="3" fill="rgba(240,235,227,0.18)"/>
                <!-- Windows row 2 -->
                <rect x="44" y="108" width="24" height="18" rx="3" fill="rgba(240,235,227,0.26)"/>
                <rect x="88" y="108" width="24" height="18" rx="3" fill="rgba(240,235,227,0.18)"/>
                <rect x="132" y="108" width="24" height="18" rx="3" fill="rgba(240,235,227,0.26)"/>
                <!-- Door -->
                <rect x="84" y="140" width="32" height="35" rx="5" fill="rgba(200,169,110,0.22)"/>
                <circle cx="110" cy="158" r="2.5" fill="rgba(200,169,110,0.55)"/>
                <!-- Ground -->
                <rect x="10" y="175" width="180" height="5" rx="2.5" fill="rgba(240,235,227,0.1)"/>
                <!-- Decorative dots -->
                <circle cx="14" cy="30" r="2.5" fill="rgba(200,169,110,0.35)"/>
                <circle cx="186" cy="22" r="3" fill="rgba(200,169,110,0.25)"/>
                <circle cx="190" cy="46" r="2" fill="rgba(240,235,227,0.2)"/>
            </svg>

            <div class="panel-headline">Panel Manajemen<br>Hotel Neo</div>
            <div class="panel-desc">Kelola seluruh operasional hotel dengan mudah dan efisien melalui satu platform terpadu.</div>


        </div>
    </div>

    <!-- Right panel -->
    <div class="panel-right">
        <div class="login-card">

            <span class="card-eyebrow">Admin Panel</span>
            <h1 class="card-title">Selamat Datang</h1>
            <p class="card-subtitle">Masukkan email dan password Anda untuk mengakses dashboard Hotel Neo.</p>

            @if(session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            @error('email')
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </div>
            @enderror

            <form id="loginForm" method="POST" action="{{ url('/login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <div class="input-wrap">
                        <input
                            type="email" name="email" id="email"
                            class="form-control"
                            placeholder="admin@hotelneo.com"
                            value="{{ old('email') }}"
                            required autocomplete="email"
                        >
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrap">
                        <input
                            type="password" name="password" id="password"
                            class="form-control has-toggle"
                            placeholder="••••••••"
                            required autocomplete="current-password"
                        >
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye toggle-pw" id="togglePw"></i>
                    </div>
                </div>

                <div class="form-extras">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="rememberMe">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="forgot-link">Lupa password?</a>
                </div>

                <button type="submit" class="btn-submit" id="loginBtn">
                    <span class="btn-label">
                        <i class="fas fa-arrow-right-to-bracket"></i>&ensp;Masuk ke Dashboard
                    </span>
                    <div class="btn-spinner"></div>
                </button>
            </form>

            <div class="divider"><span>Sistem Manajemen Hotel</span></div>

            <div class="card-footer">
                Butuh bantuan? Hubungi <a href="mailto:support@hotelneo.com">support@hotelneo.com</a>
            </div>
        </div>
    </div>

<script>
    document.getElementById('togglePw').addEventListener('click', function () {
        const pw = document.getElementById('password');
        const isHidden = pw.type === 'password';
        pw.type = isHidden ? 'text' : 'password';
        this.classList.toggle('fa-eye', !isHidden);
        this.classList.toggle('fa-eye-slash', isHidden);
    });

    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('loginBtn');
        btn.classList.add('loading');
        btn.disabled = true;
    });
</script>
</body>
</html>