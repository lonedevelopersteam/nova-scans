<!DOCTYPE html>
<html>
<head>
    <title>Kode Verifikasi OTP Anda</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        /* Client-specific Styles */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }

        /* Resets */
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* Responsive Styles */
        @media screen and (max-width: 525px) {
            .wrapper { width: 100% !important; max-width: 100% !important; }
            .padding { padding: 10px 5% 10px 5% !important; }
            .mobile-padding { padding: 10px 20px !important; }
            .section-padding { padding: 0 15px 50px 15px !important; }
            .mobile-align-center { text-align: center !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">

<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
    Kode Verifikasi OTP Anda dari {{ env('APP_NAME') }}
</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
    <tr>
        <td bgcolor="#f4f4f4" align="center" style="padding: 20px 15px 50px 15px;" class="section-padding">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                <tr>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td align="center" style="padding: 20px 0;">
                                    <a href="{{ url('/') }}" target="_blank" style="text-decoration: none;">
                                        <h1 style="font-family: Arial, sans-serif; font-size: 28px; color: #333333; margin: 0;">{{ env('APP_NAME') }}</h1>
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);">
                            <tr>
                                <td align="center" style="font-family: Arial, sans-serif; font-size: 16px; line-height: 24px; color: #333333; padding: 40px 30px 20px 30px;">
                                    <h2 style="margin: 0; font-size: 24px; color: #0073aa; padding-bottom: 20px;">Verifikasi Kode OTP Anda</h2>
                                    <p style="margin: 0;">Halo <strong style="color: #0073aa;">{{ $userName }}</strong>,</p>
                                    <p style="margin: 20px 0;">Terima kasih telah meminta kode verifikasi.</p>
                                    <p style="margin: 0;">Berikut adalah kode OTP Anda:</p>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding: 10px 30px;">
                                    <table border="0" cellpadding="0" cellspacing="0" style="border-radius: 4px; background-color: #f7f7f7; border: 1px solid #eeeeee;">
                                        <tr>
                                            <td align="center" style="font-family: 'Courier New', monospace; font-size: 36px; font-weight: bold; color: #333333; padding: 15px 30px;">
                                                {{ $otpCode }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="font-family: Arial, sans-serif; font-size: 14px; line-height: 22px; color: #666666; padding: 20px 30px 40px 30px;">
                                    <p style="margin: 0;">Kode ini akan kedaluwarsa dalam <strong>{{ $expiresInMinutes }} menit</strong>.</p>
                                    <p style="margin: 10px 0 0 0; color: #dc3545; font-weight: bold;">Jangan pernah membagikan kode ini kepada siapapun.</p>
                                </td>
                            </tr>
                        </table>

                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td align="center" style="padding: 30px 0 0 0; font-family: Arial, sans-serif; font-size: 12px; line-height: 18px; color: #999999;">
                                    <p style="margin: 0;">Salam Hormat,</p>
                                    <p style="margin: 5px 0 0 0;">Tim {{ env('APP_NAME') }}</p>
                                    <p style="margin: 10px 0 0 0;">
                                        &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All Rights Reserved.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
