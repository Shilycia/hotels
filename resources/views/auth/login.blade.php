<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login Admin - Hotel Neo</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --color-info: #17c1e8;
            --color-info-dark: #0ea5e4;
            --color-primary: #cb0c9f;
            --color-dark: #344767;
            --color-text: #67748e;
            --color-light: #f8f9fa;
            --gradient-info: linear-gradient(310deg, #2152ff, #21d4fd);
            --gradient-primary: linear-gradient(310deg, #7928ca, #ff0080);
            --shadow-card: 0 20px 27px 0 rgba(0,0,0,.05);
            --shadow-colored: 0 4px 7px -1px rgba(0,0,0,.11), 0 2px 4px -1px rgba(0,0,0,.07);
            --border-radius: 1rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Background Split ── */
        .page-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .bg-left {
            position: fixed;
            top: 0; left: 0;
            width: 40%;
            height: 100%;
            background: var(--gradient-info);
            z-index: 0;
            overflow: hidden;
        }

        .bg-left::before {
            content: '';
            position: absolute;
            width: 380px; height: 380px;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
            top: -80px; left: -80px;
        }

        .bg-left::after {
            content: '';
            position: absolute;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,.06);
            bottom: 60px; right: -60px;
        }

        .bg-left-inner {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            z-index: 1;
            text-align: center;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 2.5rem;
        }

        .brand-logo .logo-icon {
            width: 48px; height: 48px;
            background: rgba(255,255,255,.25);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(6px);
            font-size: 1.4rem;
            color: #fff;
        }

        .brand-logo span {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.3px;
        }

        .bg-illustration {
            width: 220px;
            opacity: .9;
            margin-bottom: 2rem;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,.2));
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }

        .bg-left h2 {
            color: #fff;
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: .75rem;
        }

        .bg-left p {
            color: rgba(255,255,255,.78);
            font-size: .92rem;
            line-height: 1.7;
            max-width: 280px;
        }

        /* ── Right Content ── */
        .content-right {
            margin-left: 40%;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-card);
            padding: 2.5rem;
            animation: slideUp .5s cubic-bezier(.23,1,.32,1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-header-area {
            margin-bottom: 1.8rem;
        }

        .card-header-area .badge-top {
            display: inline-block;
            background: linear-gradient(310deg,#2152ff15,#21d4fd25);
            color: var(--color-info-dark);
            font-size: .72rem;
            font-weight: 600;
            padding: .3rem .85rem;
            border-radius: 50px;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: .85rem;
        }

        .card-header-area h3 {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--color-dark);
            margin-bottom: .4rem;
            background: var(--gradient-info);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-header-area p {
            font-size: .875rem;
            color: var(--color-text);
            font-weight: 400;
        }

        /* ── Form ── */
        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-group label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            color: var(--color-dark);
            margin-bottom: .45rem;
            letter-spacing: .3px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: .85rem;
            pointer-events: none;
            transition: color .2s;
        }

        .input-wrapper .toggle-pw {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: .85rem;
            cursor: pointer;
            transition: color .2s;
        }

        .form-control {
            width: 100%;
            padding: .75rem 1rem .75rem 2.7rem;
            font-family: inherit;
            font-size: .875rem;
            font-weight: 400;
            color: var(--color-dark);
            background: #fff;
            border: 1px solid #e6e9ed;
            border-radius: .65rem;
            outline: none;
            transition: border-color .25s, box-shadow .25s;
            -webkit-appearance: none;
        }

        .form-control:focus {
            border-color: var(--color-info);
            box-shadow: 0 0 0 3px rgba(23,193,232,.15);
        }

        .form-control:focus ~ .input-icon {
            color: var(--color-info);
        }

        .form-control::placeholder {
            color: #b2bec3;
        }

        /* Remember & Forgot */
        .form-extras {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.4rem;
        }

        .remember-check {
            display: flex;
            align-items: center;
            gap: .45rem;
            cursor: pointer;
        }

        .remember-check input[type=checkbox] {
            width: 16px; height: 16px;
            accent-color: var(--color-info);
            cursor: pointer;
        }

        .remember-check span {
            font-size: .82rem;
            color: var(--color-text);
        }

        .forgot-link {
            font-size: .82rem;
            color: var(--color-info-dark);
            font-weight: 500;
            text-decoration: none;
            transition: opacity .2s;
        }

        .forgot-link:hover { opacity: .7; }

        /* Button */
        .btn-login {
            width: 100%;
            padding: .8rem 1.5rem;
            font-family: inherit;
            font-size: .9rem;
            font-weight: 600;
            color: #fff;
            background: var(--gradient-info);
            border: none;
            border-radius: .75rem;
            cursor: pointer;
            box-shadow: 0 4px 7px -1px rgba(0,0,0,.11), 0 2px 4px -1px rgba(0,0,0,.07), inset 0 1px 0 rgba(255,255,255,.2);
            transition: transform .18s, box-shadow .18s, opacity .18s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background .2s;
        }

        .btn-login:hover::before {
            background: rgba(255,255,255,.08);
        }

        .btn-login:active {
            transform: scale(.98);
        }

        .btn-login:disabled {
            opacity: .7;
            cursor: not-allowed;
        }

        .btn-login .spinner {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .65s linear infinite;
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-login.loading .spinner { display: block; }
        .btn-login.loading .btn-text { display: none; }

        /* Alert */
        .alert {
            padding: .8rem 1rem;
            border-radius: .65rem;
            font-size: .82rem;
            font-weight: 500;
            margin-bottom: 1rem;
            display: none;
            align-items: center;
            gap: .5rem;
        }

        .alert.alert-danger {
            background: #fff5f5;
            color: #e74c3c;
            border: 1px solid #fdd;
            display: flex;
        }

        .alert.alert-success {
            background: #f0fff4;
            color: #27ae60;
            border: 1px solid #c3e6cb;
            display: flex;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: .85rem;
            margin: 1.4rem 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            font-size: .78rem;
            color: #adb5bd;
            font-weight: 500;
            white-space: nowrap;
        }

        /* Footer note */
        .card-footer-note {
            text-align: center;
            margin-top: 1.2rem;
            font-size: .8rem;
            color: var(--color-text);
        }

        .card-footer-note a {
            color: var(--color-info-dark);
            font-weight: 600;
            text-decoration: none;
        }

        /* Stats row */
        .stats-row {
            display: flex;
            gap: .75rem;
            margin-top: 2.5rem;
        }

        .stat-item {
            background: rgba(255,255,255,.18);
            backdrop-filter: blur(8px);
            border-radius: .85rem;
            padding: .9rem 1.1rem;
            flex: 1;
            text-align: center;
            border: 1px solid rgba(255,255,255,.25);
        }

        .stat-item .stat-num {
            font-size: 1.3rem;
            font-weight: 700;
            color: #fff;
            line-height: 1;
            margin-bottom: .25rem;
        }

        .stat-item .stat-label {
            font-size: .72rem;
            color: rgba(255,255,255,.75);
            font-weight: 500;
            letter-spacing: .3px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .bg-left { display: none; }
            .content-right { margin-left: 0; background: var(--gradient-info); }
            .login-card { box-shadow: 0 25px 50px rgba(0,0,0,.15); }
        }
    </style>
</head>
<body>

<div class="page-wrapper">

    <!-- LEFT PANEL -->
    <div class="bg-left">
        <div class="bg-left-inner">
            <div class="brand-logo">
                <div class="logo-icon">
                    <i class="fas fa-hotel"></i>
                </div>
                <span>Hotel Neo</span>
            </div>

            <!-- Illustration: hotel/building SVG -->
            <svg class="bg-illustration" viewBox="0 0 280 220" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Building body -->
                <rect x="60" y="70" width="160" height="140" rx="8" fill="rgba(255,255,255,0.25)"/>
                <!-- Roof accent -->
                <rect x="70" y="55" width="140" height="18" rx="5" fill="rgba(255,255,255,0.35)"/>
                <!-- Flagpole -->
                <line x1="140" y1="20" x2="140" y2="55" stroke="rgba(255,255,255,0.6)" stroke-width="2.5"/>
                <polygon points="140,20 162,30 140,38" fill="rgba(255,255,255,0.7)"/>
                <!-- Windows row 1 -->
                <rect x="80" y="88" width="28" height="22" rx="4" fill="rgba(255,255,255,0.55)"/>
                <rect x="126" y="88" width="28" height="22" rx="4" fill="rgba(255,255,255,0.55)"/>
                <rect x="172" y="88" width="28" height="22" rx="4" fill="rgba(255,255,255,0.4)"/>
                <!-- Windows row 2 -->
                <rect x="80" y="124" width="28" height="22" rx="4" fill="rgba(255,255,255,0.4)"/>
                <rect x="126" y="124" width="28" height="22" rx="4" fill="rgba(255,255,255,0.55)"/>
                <rect x="172" y="124" width="28" height="22" rx="4" fill="rgba(255,255,255,0.55)"/>
                <!-- Door -->
                <rect x="119" y="172" width="42" height="38" rx="6" fill="rgba(255,255,255,0.35)"/>
                <circle cx="154" cy="192" r="3" fill="rgba(255,255,255,0.7)"/>
                <!-- Ground -->
                <rect x="20" y="210" width="240" height="8" rx="4" fill="rgba(255,255,255,0.18)"/>
                <!-- Stars / decorative dots -->
                <circle cx="30" cy="40" r="3" fill="rgba(255,255,255,0.5)"/>
                <circle cx="250" cy="60" r="4" fill="rgba(255,255,255,0.4)"/>
                <circle cx="260" cy="30" r="2.5" fill="rgba(255,255,255,0.35)"/>
            </svg>

            <h2>Panel Manajemen<br>Hotel Neo</h2>
            <p>Kelola semua operasional hotel Anda dengan mudah dan efisien melalui satu platform terpadu.</p>

            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-num">128</div>
                    <div class="stat-label">Kamar</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">94%</div>
                    <div class="stat-label">Hunian</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">4.8★</div>
                    <div class="stat-label">Rating</div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="content-right">
        <div class="login-card">
            <div class="card-header-area">
                <span class="badge-top">Admin Panel</span>
                <h3>Selamat Datang</h3>
                <p>Masukkan email dan password Anda untuk mengakses Neo admin</p>
            </div>

            <!-- Alert Area -->
            <div class="alert" id="alertBox" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span id="alertMsg">Terjadi kesalahan.</span>
            </div>

            <form id="loginForm" method="POST" action="{{ url('/login') }}">
                @csrf

                @error('email')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-bottom: 1rem;">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" id="email" class="form-control" placeholder="admin@hotelneo.com" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autocomplete="current-password" style="padding-right:2.8rem">
                        <i class="fas fa-eye toggle-pw" id="togglePw" title="Tampilkan password" style="cursor: pointer;"></i>
                    </div>
                </div>

                <div class="form-extras">
                    <label class="remember-check">
                        <input type="checkbox" name="remember" id="rememberMe">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="forgot-link">Lupa password?</a>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span class="btn-text"><i class="fas fa-arrow-right-to-bracket"></i>&ensp;Masuk ke Dashboard</span>
                    <div class="spinner" style="display: none;"></div>
                </button>
            </form>

            <div class="divider"><span>Sistem Manajemen Hotel</span></div>

            <div class="card-footer-note">
                Butuh bantuan? Hubungi &nbsp;<a href="mailto:support@hotelneo.com">support@hotelneo.com</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePw').addEventListener('click', function (e) {
        const passwordInput = document.getElementById('password');
        const icon = this;
        
        // Toggle tipe input antara password dan text
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash'); // Ganti ikon jadi mata dicoret
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye'); // Ganti kembali ke mata normal
        }
    });

    // (Opsional) Tampilkan spinner saat tombol submit diklik
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btnText = document.querySelector('.btn-text');
        const spinner = document.querySelector('.spinner');
        
        btnText.style.display = 'none';
        spinner.style.display = 'inline-block';
    });
</script>
</body>
</html>