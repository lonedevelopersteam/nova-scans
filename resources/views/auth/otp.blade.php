<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tambahkan meta api key -->
    <meta name="api-key" content="{{ env('API_KEY') }}">

    <style>
        body {
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .container {
            max-width: 400px;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-submit,
        .btn-resend {
            background-color: #0073aa;
            border-color: #0073aa;
            color: #fff;
            width: 100%;
        }
        .btn-resend[disabled] {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .back-link {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h3 class="text-center mb-4">Verifikasi Kode OTP</h3>

    <div id="alertContainer" class="mb-3"></div>

    <p class="text-muted text-center mb-3" id="otp_message">
        Masukkan kode OTP 6 digit yang telah kami kirimkan ke email Anda.
        Kode akan kedaluwarsa pada: <strong id="otp_expiry_time"></strong>
    </p>

    <p class="text-center" id="user_id_display"></p>

    <form id="otpVerificationForm">
        <div class="mb-3">
            <label for="otp_code" class="form-label">Kode OTP</label>
            <input type="text" class="form-control text-center fs-4" id="otp_code" required maxlength="6" pattern="\d{6}" title="Masukkan 6 digit angka" autofocus>
        </div>
        <button type="submit" class="btn btn-submit mb-3" id="verifyButton">Verifikasi</button>
    </form>

    <div class="d-flex justify-content-center">
        <button id="resend-button" class="btn btn-resend" disabled>Kirim Ulang Kode (60)</button>
    </div>

    <div class="back-link">
        <a href="{{ route('login') }}">Kembali ke Halaman Login</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const otpVerificationForm = document.getElementById('otpVerificationForm');
        const verifyButton = document.getElementById('verifyButton');
        const otpCodeInput = document.getElementById('otp_code');
        const resendButton = document.getElementById('resend-button');
        const alertContainer = document.getElementById('alertContainer');
        const otpExpiryTimeElement = document.getElementById('otp_expiry_time');
        const otpMessageElement = document.getElementById('otp_message');

        function getCookie(name) {
            const cookies = document.cookie.split(';');
            for (const cookie of cookies) {
                const [key, value] = cookie.trim().split('=');
                if (key === name) return decodeURIComponent(value);
            }
            return null;
        }

        function setCookie(name, value, minutes) {
            const expires = new Date(Date.now() + minutes * 60 * 1000).toUTCString();
            document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/`;
        }

        function deleteCookie(name) {
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        }

        const userId = getCookie('otp_user_id');
        const email = getCookie('otp_user_email');
        const expiresAt = getCookie('otp_expires_at');
        const apiKey = document.querySelector('meta[name="api-key"]')?.content;

        if (!userId || !email || !expiresAt) {
            window.location.href = "{{ route('password') }}";
            return;
        }

        let otpExpiresAt = new Date(parseInt(expiresAt) * 1000);

        function showAlert(message, type = 'danger') {
            alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
        }

        function updateOtpExpiryDisplay() {
            const now = new Date();
            if (now.getTime() < otpExpiresAt.getTime()) {
                otpExpiryTimeElement.textContent = otpExpiresAt.toLocaleTimeString('id-ID', {
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                });
            } else {
                otpMessageElement.innerHTML = `Kode OTP Anda sudah kedaluwarsa. Silakan minta OTP baru.`;
                otpVerificationForm.style.display = 'none';
                resendButton.style.display = 'none';
                const newOtpLink = document.createElement('div');
                newOtpLink.classList.add('mt-3');
                newOtpLink.innerHTML = `<a href="{{ route('password') }}" class="btn btn-outline-secondary">Minta OTP Baru</a>`;
                otpMessageElement.parentNode.insertBefore(newOtpLink, otpMessageElement.nextSibling);
            }
        }

        function startResendTimer() {
            let countdown = 60;
            resendButton.disabled = true;
            resendButton.textContent = `Kirim Ulang Kode (${countdown})`;

            const interval = setInterval(() => {
                countdown--;
                resendButton.textContent = `Kirim Ulang Kode (${countdown})`;
                if (countdown <= 0) {
                    clearInterval(interval);
                    resendButton.disabled = false;
                    resendButton.textContent = 'Kirim Ulang Kode';
                }
            }, 1000);
        }

        resendButton.addEventListener('click', async function () {
            resendButton.disabled = true;
            resendButton.textContent = 'Mengirim...';
            alertContainer.innerHTML = '';

            const baseUrl = "{{ url('/') }}";
            const apiUrl = `${baseUrl}/api/v1/users/otp`;

            try {
                const csrfToken = document.querySelector('input[name="_token"]')?.value || '';
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': apiKey,
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ email: email })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const expireMinutes = parseInt(data.data.otp_expire);
                    const now = new Date();
                    const expiresTimestamp = Math.floor(now.getTime() / 1000) + (expireMinutes * 60);

                    setCookie('otp_user_id', data.data.user_id, expireMinutes);
                    setCookie('otp_user_email', email, expireMinutes);
                    setCookie('otp_expires_at', expiresTimestamp, expireMinutes);

                    otpExpiresAt = new Date(expiresTimestamp * 1000);
                    updateOtpExpiryDisplay();
                    startResendTimer();

                    showAlert(data.message || 'OTP berhasil dikirim ulang!', 'success');
                } else {
                    showAlert(data.message || 'Gagal mengirim ulang OTP.', 'danger');
                }
            } catch (error) {
                console.error('Resend Error:', error);
                showAlert('Gagal terhubung ke server.', 'danger');
            }
        });

        otpVerificationForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const otpCode = otpCodeInput.value;

            if (!otpCode || otpCode.length !== 6 || !/^\d{6}$/.test(otpCode)) {
                showAlert('Kode OTP harus 6 digit angka.');
                return;
            }

            verifyButton.disabled = true;
            verifyButton.textContent = 'Memverifikasi...';
            alertContainer.innerHTML = '';

            try {
                const baseUrl = "{{ url('/') }}";
                const apiUrl = `${baseUrl}/api/v1/users/check-otp`;

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': apiKey
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        code: otpCode
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showAlert(data.message || 'OTP berhasil diverifikasi!', 'success');

                    // Hapus cookies
                    deleteCookie('otp_user_email');
                    deleteCookie('otp_expires_at');

                    setTimeout(() => {
                        window.location.href = "{{ route('ganti-password') }}";
                    }, 1500);
                } else {
                    showAlert(data.message || 'Kode OTP tidak valid.', 'danger');
                }

            } catch (error) {
                console.error('OTP Verification Error:', error);
                showAlert('Terjadi kesalahan saat verifikasi OTP.', 'danger');
            } finally {
                verifyButton.disabled = false;
                verifyButton.textContent = 'Verifikasi';
            }
        });

        // Inisialisasi
        updateOtpExpiryDisplay();
        setInterval(updateOtpExpiryDisplay, 1000);
        startResendTimer();
    });
</script>

</body>
</html>
