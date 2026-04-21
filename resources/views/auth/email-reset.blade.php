<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #1a1f2e; text-align: center;">Reset Password Hotelier</h2>
        <p style="color: #6c757d; font-size: 16px;">Halo,</p>
        <p style="color: #6c757d; font-size: 16px;">Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $link }}" style="background-color: #f39c12; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Ganti Password Sekarang</a>
        </div>

        <p style="color: #6c757d; font-size: 14px;">Jika Anda tidak merasa meminta reset password, abaikan saja email ini.</p>
        <hr style="border: none; border-top: 1px solid #e9ecef; margin: 20px 0;">
        <p style="color: #adb5bd; font-size: 12px; text-align: center;">&copy; {{ date('Y') }} Hotelier. All rights reserved.</p>
    </div>
</body>
</html>