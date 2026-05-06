<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Atur Ulang Kata Sandi</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; border-top: 4px solid #c8a96e; text-align: center;">
        
        <h2 style="color: #2c2420; margin-bottom: 10px;">Atur Ulang Kata Sandi Anda</h2>
        <p style="color: #6c757d; font-size: 14px; line-height: 1.6; margin-bottom: 30px;">
            Anda menerima email ini karena kami menerima permintaan atur ulang kata sandi untuk akun Hotel Neo Anda. Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.
        </p>

        <a href="{{ $url }}" style="background-color: #c8a96e; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            Ganti Kata Sandi
        </a>

        <p style="color: #adb5bd; font-size: 12px; margin-top: 30px;">
            Tautan ini akan kedaluwarsa dalam 60 menit.<br>
            Jika tombol di atas tidak berfungsi, *copy-paste* URL berikut ke browser Anda:<br>
            <a href="{{ $url }}" style="color: #c8a96e; word-break: break-all;">{{ $url }}</a>
        </p>
    </div>
</body>
</html>