<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Penyewaan Dibatalkan</title>
</head>

<body style="background-color: #f4f4f4; margin: 0; padding: 0; font-family: Arial, sans-serif;">

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0"
                    style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #dc3545; padding: 20px; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; font-size: 24px; color: #ffffff;">Penyewaan Dibatalkan</h1>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px; color: #333333; font-size: 16px; line-height: 1.5;">
                            <p style="margin: 0 0 20px 0; text-align: center;">
                                Halo, {{ $name }}. Kami mohon maaf, tetapi pemesanan lapangan futsal Anda telah dibatalkan karena identitas yang diberikan tidak valid.
                            </p>
                            <p style="text-align: center;">Booking: <strong>{{ $booking->invoice }}</strong></p>
                            <p style="margin: 0; text-align: center;">
                                Jika Anda merasa ini adalah kesalahan atau memiliki pertanyaan, silakan hubungi admin kami untuk klarifikasi.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f4f4f4; padding: 20px; text-align: center; font-size: 14px; color: #666666; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0; font-weight: bold;">Butuh Bantuan?</p>
                            <p style="margin: 0;"><a href="#" style="color: #dc3545; text-decoration: none;">Hubungi Kami</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
