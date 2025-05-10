<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Konfirmasi Penyewaan Lapangan</title>
</head>

<body style="background-color: #f4f4f4; margin: 0; padding: 0; font-family: Arial, sans-serif;">

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0"
                    style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #28a745; padding: 20px; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; font-size: 24px; color: #ffffff;">Konfirmasi Penyewaan</h1>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px; color: #333333; font-size: 16px; line-height: 1.5;">
                            <p style="margin: 0 0 20px 0; text-align: center;">
                                Halo, {{ $name }}. Pemesanan lapangan futsal Anda telah dikonfirmasi!
                                Segera lakukan pembayaran sebelum waktu berikut:
                            </p>
                            <h3 style="text-align: center; color: #28a745;">{{ $expired_at }}</h3>
                            <p style="text-align: center;">Booking: <strong>{{ $booking->invoice }}</strong></p>
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ route('bookings.show', ['booking' => $booking]) }}"
                                    style="display: inline-block; background-color: #28a745; color: #ffffff; text-decoration: none; padding: 12px 25px; font-size: 18px; border-radius: 5px;">
                                    Bayar Sekarang
                                </a>
                            </div>
                            <p style="margin: 0; text-align: center;">
                                Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut di browser Anda:
                            </p>
                            <p style="margin: 0 0 20px 0; text-align: center; color: #28a745;">
                                <a href="{{ route('bookings.show', ['booking' => $booking]) }}" style="color: #28a745; text-decoration: none;">
                                    {{ route('bookings.show', ['booking' => $booking]) }}
                                </a>
                            </p>
                            <p style="margin: 0; text-align: center;">
                                Jika ada pertanyaan, balas email ini kapan saja. Kami siap membantu Anda!
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f4f4f4; padding: 20px; text-align: center; font-size: 14px; color: #666666; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0; font-weight: bold;">Butuh Bantuan?</p>
                            <p style="margin: 0;"><a href="#" style="color: #28a745; text-decoration: none;">Hubungi Kami</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
