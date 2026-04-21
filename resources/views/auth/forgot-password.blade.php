<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lupa Password – Hotel Neo</title>
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

        .panel-illustration {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .panel-icon-wrap {
            width: 96px; height: 96px;
            border-radius: 50%;
            background: rgba(200,169,110,0.1);
            border: 1px solid rgba(200,169,110,0.18);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 32px;
            animation: pulse 3s ease-in-out infinite;
        }

        .panel-icon-wrap i {
            font-size: 36px;
            color: #c8a96e;
            opacity: 0.85;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%       { transform: scale(1.05); opacity: 0.8; }
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

        /* Steps */
        .panel-steps {
            margin-top: 40px;
            width: 100%;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .step-item:last-child { border-bottom: none; }

        .step-num {
            width: 22px; height: 22px;
            border-radius: 50%;
            background: rgba(200,169,110,0.18);
            border: 1px solid rgba(200,169,110,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 10px;
            font-weight: 500;
            color: #e2c98a;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .step-text {
            font-size: 12px;
            color: rgba(240,235,227,0.45);
            line-height: 1.5;
        }

        .step-text strong {
            color: rgba(240,235,227,0.75);
            font-weight: 500;
            display: block;
            margin-bottom: 2px;
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

        .forgot-card {
            width: 100%;
            max-width: 400px;
            animation: fadeUp .35s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Back link */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--ink3);
            text-decoration: none;
            margin-bottom: 28px;
            transition: color .16s;
        }

        .back-link:hover { color: var(--bark); }
        .back-link i { font-size: 11px; }

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
            margin-bottom: 28px;
            line-height: 1.6;
        }

        /* Alert */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 11px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .alert i { margin-top: 1px; flex-shrink: 0; }

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
        .form-group { margin-bottom: 18px; }

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

        .form-control:focus + .input-icon { color: var(--bark); }
        .input-wrap:focus-within .input-icon { color: var(--bark); }

        .form-control::placeholder { color: var(--stone); }

        .form-hint {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 5px;
            line-height: 1.5;
        }

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
            margin-bottom: 16px;
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
            margin: 6px 0 16px;
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

        /* Success state */
        .success-state {
            display: none;
            text-align: center;
            padding: 12px 0;
        }

        .success-state.show { display: block; }

        .success-icon-wrap {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: var(--moss-soft);
            border: 1px solid #b7d8c2;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }

        .success-icon-wrap i {
            font-size: 24px;
            color: var(--moss);
        }

        .success-title {
            font-family: 'Lora', serif;
            font-size: 20px;
            color: var(--ink);
            font-style: italic;
            margin-bottom: 8px;
        }

        .success-desc {
            font-size: 13px;
            color: var(--ink3);
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .btn-outline {
            width: 100%;
            padding: 11px 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            color: var(--bark);
            background: transparent;
            border: 1px solid var(--sand3);
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all .18s;
            text-decoration: none;
        }

        .btn-outline:hover {
            border-color: var(--bark);
            background: var(--bark-soft);
        }

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
            <div class="panel-icon-wrap">
                <i class="fas fa-key"></i>
            </div>

            <div class="panel-headline">Pulihkan Akses<br>Anda</div>
            <div class="panel-desc">Ikuti langkah sederhana berikut untuk mengatur ulang password akun admin Anda.</div>

            <div class="panel-steps">
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-text">
                        <strong>Masukkan email</strong>
                        Ketik alamat email yang terdaftar di sistem.
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-text">
                        <strong>Cek inbox Anda</strong>
                        Kami akan mengirim tautan reset ke email tersebut.
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-text">
                        <strong>Buat password baru</strong>
                        Klik tautan dan atur password baru Anda.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right panel -->
    <div class="panel-right">
        <div class="forgot-card">

            <a href="{{ url('/login') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke halaman login
            </a>

            {{-- Form state --}}
            <div id="formState">
                <span class="card-eyebrow">Reset Password</span>
                <h1 class="card-title">Lupa Password?</h1>
                <p class="card-subtitle">Tidak perlu khawatir. Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password.</p>

                @if(session('status'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form id="forgotForm" method="POST" action="{{ route('password.email') }}">
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
                        <div class="form-hint">Gunakan email yang terdaftar sebagai akun admin.</div>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span class="btn-label">
                            <i class="fas fa-paper-plane"></i>&ensp;Kirim Tautan Reset
                        </span>
                        <div class="btn-spinner"></div>
                    </button>
                </form>

                <div class="divider"><span>atau</span></div>

                <div class="card-footer">
                    Sudah ingat password? <a href="{{ url('/login') }}">Masuk sekarang</a>
                </div>
            </div>

            {{-- Success state (shown after submit if session status present) --}}
            @if(session('status'))
            <div class="success-state show" id="successState">
                <div class="success-icon-wrap">
                    <i class="fas fa-envelope-circle-check"></i>
                </div>
                <div class="success-title">Email Terkirim!</div>
                <div class="success-desc">
                    Tautan reset password telah dikirim ke <strong>{{ old('email', request('email')) }}</strong>.
                    Silakan cek inbox atau folder spam Anda.
                </div>
                <a href="{{ url('/login') }}" class="btn-outline">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>
            </div>
            @endif

        </div>
    </div>

<script>
    document.getElementById('forgotForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.classList.add('loading');
        btn.disabled = true;
    });
</script>
</body>
</html>