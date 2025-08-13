<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - {{ env('APP_NAME', 'Default Title') }}</title>
    <link rel="icon" href="{{ asset('icon.png') }}" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <meta name="api-key" content="{{ env('API_KEY') }}">

    <style>
        body {
            background-color: #f3f4f6; /* Latar belakang abu-abu muda */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .container {
            max-width: 400px; /* Lebar container lebih kecil untuk form ringkas */
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        .btn-submit {
            background-color: #0073aa; /* Warna biru WordPress */
            border-color: #0073aa;
            color: #fff;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 4px;
            width: 100%; /* Tombol lebar penuh */
        }
        .btn-submit:hover {
            background-color: #005a87;
            border-color: #005a87;
            color: #fff;
        }
        .back-link {
            text-align: center;
            margin-top: 1rem;
        }
        .back-link a {
            color: #0073aa;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .alert-container {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center mb-4">Lupa Password</h3>

    <div id="alertContainer" class="alert-container">
        {{-- Area untuk menampilkan pesan alert dari server atau JS --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <form id="forgotPasswordForm" method="POST">
        @csrf {{-- Penting untuk keamanan Laravel --}}
        <p class="text-muted text-center mb-4">Masukkan alamat email Anda untuk menerima OTP.</p>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" autocomplete="off" required>
        </div>

        <button type="submit" class="btn btn-submit mb-3" id="submitButton">Kirim</button>
    </form>

    <div class="back-link">
        <a href="{{ route('login') }}">Kembali ke Halaman Login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        const submitButton = document.getElementById('submitButton');
        const alertContainer = document.getElementById('alertContainer');

        function showAlert(message, type) {
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        }

        // Set cookie dari JavaScript
        function setCookie(name, value, minutes) {
            const date = new Date();
            date.setTime(date.getTime() + (minutes * 60 * 1000));
            const expires = "expires=" + date.toUTCString();
            const cookieString = `${name}=${value}; ${expires}; path=/; SameSite=Lax`;
            document.cookie = cookieString;
            console.log(`Cookie set: ${cookieString}`);
        }

        forgotPasswordForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            submitButton.disabled = true;
            submitButton.textContent = 'Mengirim...';
            alertContainer.innerHTML = '';

            const email = document.getElementById('email').value;
            const csrfToken = document.querySelector('input[name="_token"]').value;
            const apiKey = document.querySelector('meta[name="api-key"]').content;

            try {
                const baseUrl = "{{ url('/') }}";
                const apiUrl = `${baseUrl}/api/v1/users/otp`;

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Authorization': apiKey
                    },
                    body: JSON.stringify({ email: email })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Set cookie via JS
                    const expireMinutes = parseInt(data.data.otp_expire);
                    setCookie('otp_user_id', data.data.user_id, expireMinutes);
                    setCookie('otp_user_email', email, expireMinutes);

                    const now = new Date();
                    const expiresTimestamp = Math.floor(now.getTime() / 1000) + (expireMinutes * 60);
                    setCookie('otp_expires_at', expiresTimestamp, expireMinutes);

                    showAlert(data.message || 'OTP berhasil dikirim ke email Anda!', 'success');

                    // Delay redirect agar cookie tersimpan dulu
                    setTimeout(() => {
                        window.location.href = "{{ route('otp') }}";
                    }, 1000);
                } else {
                    if (data.message) {
                        showAlert(data.message, 'danger');
                    } else if (data.errors) {
                        let errorMessages = '';
                        for (const key in data.errors) {
                            errorMessages += `<li>${data.errors[key].join(', ')}</li>`;
                        }
                        showAlert(`<ul>${errorMessages}</ul>`, 'danger');
                    } else {
                        showAlert(`Terjadi kesalahan server. Status: ${response.status}.`, 'danger');
                    }
                }

            } catch (error) {
                console.error('Fetch Error:', error);
                showAlert('Gagal terhubung ke server. Pastikan Anda memiliki koneksi internet.', 'danger');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Kirim';
            }
        });
    });
</script>

</body>
</html>
