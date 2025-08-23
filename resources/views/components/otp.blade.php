<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kode OTP Anda</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f4f7; font-family:Arial, sans-serif;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0"
        style="max-width:600px; margin:0 auto; background-color:#ffffff; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
        <tr>
            <td style="padding:30px; text-align:center; border-bottom:1px solid #eee;">
                <h2 style="margin:0; color:#333333;">🔐 Verifikasi OTP</h2>
            </td>
        </tr>

        <tr>
            <td style="padding:30px; text-align:center;">
                <p style="font-size:16px; color:#555555; margin:0 0 20px;">
                    Halo <strong>{{ Auth::user()->name ?? 'Pengguna' }}</strong>,
                </p>
                <p style="font-size:15px; color:#555555; margin:0 0 25px;">
                    Gunakan kode OTP berikut untuk menyelesaikan proses verifikasi Anda.
                </p>

                <div
                    style="display:inline-block; padding:15px 25px; background-color:#4F46E5; color:#ffffff; font-size:24px; letter-spacing:5px; border-radius:8px; font-weight:bold;">
                    {{ $otp }}
                </div>

                <p style="margin-top:25px; font-size:14px; color:#888888;">
                    Kode ini hanya berlaku selama <strong>5 menit</strong>.
                </p>
            </td>
        </tr>

        <tr>
            <td
                style="padding:20px 30px; background-color:#f9fafb; text-align:center; font-size:12px; color:#aaaaaa; border-top:1px solid #eee;">
                <p style="margin:0;">
                    Jika Anda tidak meminta OTP ini, abaikan email ini.
                </p>
            </td>
        </tr>
    </table>

</body>

</html>
