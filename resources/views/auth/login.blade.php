<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Login Staf - Hotel Neo</title>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: #f7f3ee; color: #2c2420; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-box { background: #fff; width: 100%; max-width: 400px; border-radius: 12px; padding: 40px; box-shadow: 0 10px 30px rgba(44,36,32,0.08); border: 1px solid #ede8e0; text-align: center; }
        .brand-icon { width: 50px; height: 50px; border-radius: 12px; background: #c8a96e; display: inline-flex; align-items: center; justify-content: center; font-size: 20px; color: #2c2820; margin-bottom: 15px; }
        .title { font-family: 'Lora', serif; font-size: 24px; color: #2c2420; margin-bottom: 5px; }
        .subtitle { font-size: 13px; color: #9e9088; margin-bottom: 30px; }
        
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-label { display: block; font-size: 11px; font-weight: 600; color: #6b5e54; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { width: 100%; padding: 12px 15px; border: 1px solid #e4ddd3; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 14px; background: #f7f3ee; outline: none; transition: border-color 0.2s ease; }
        .form-control:focus { border-color: #8b7355; background: #fff; }
        
        .btn-login { width: 100%; padding: 14px; background: #8b7355; color: #fff; border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s ease; margin-top: 10px; }
        .btn-login:hover { background: #7a6448; }

        .alert-error { background: #fceee9; color: #8a3a24; border: 1px solid #f5c4b0; padding: 10px; border-radius: 8px; font-size: 12px; margin-bottom: 20px; text-align: left; display: flex; align-items: center; gap: 8px;}
    </style>
</head>
<body>

    <div class="login-box">
        <div class="brand-icon"><i class="fas fa-hotel"></i></div>
        <h1 class="title">Hotel Neo</h1>
        <p class="subtitle">Masuk ke Portal Back-Office</p>

        @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Alamat Email Staf</label>
                <input type="email" name="email" class="form-control" placeholder="admin@hotelneo.com" required value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Kata Sandi</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div style="text-align: left; margin-bottom: 20px; font-size: 13px; color: #6b5e54;">
                <label style="cursor: pointer;">
                    <input type="checkbox" name="remember" style="margin-right: 5px;"> Ingat saya di perangkat ini
                </label>
            </div>
            <button type="submit" class="btn-login">Masuk ke Dasbor</button>
        </form>
    </div>

</body>
</html>