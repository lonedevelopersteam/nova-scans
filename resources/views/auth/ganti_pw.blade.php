<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - {{ env('APP_NAME', 'Default Title') }}</title>

    <meta name="api-key" content="{{ env('API_KEY') }}">
    <link rel="icon" href="{{ asset('icon.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .container {
            max-width: 400px;
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
            background-color: #0073aa;
            border-color: #0073aa;
            color: #fff;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 4px;
            width: 100%;
        }
        .btn-submit:hover {
            background-color: #005a87;
            border-color: #005a87;
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
        .input-group-text {
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center mb-4">Ganti Password</h3>

    <div id="alertContainer"></div>

    <form id="passwordForm">
        @csrf
        <p class="text-muted text-center mb-4">Masukkan password baru Anda.</p>

        <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" required>
                <button class="btn btn-outline-secondary input-group-text" type="button" id="togglePassword">Show</button>
            </div>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
            <div class="input-group">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <button class="btn btn-outline-secondary input-group-text" type="button" id="toggleConfirmPassword">Show</button>
            </div>
        </div>

        <button type="submit" class="btn btn-submit mb-3" id="submitButton">Ganti Password</button>
    </form>

    <div class="back-link">
        <a href="{{ route('login') }}">Kembali ke Halaman Login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordForm = document.getElementById('passwordForm');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const alertContainer = document.getElementById('alertContainer');
        const submitButton = document.getElementById('submitButton');

        function getCookie(name) {
            const cookies = document.cookie.split(';');
            for (const cookie of cookies) {
                const [key, value] = cookie.trim().split('=');
                if (key === name) return decodeURIComponent(value);
            }
            return null;
        }

        function deleteCookie(name) {
            document.cookie = `${name}=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;`;
        }

        function showAlert(message, type = 'danger') {
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
        }

        // Cookie check
        const userId = getCookie('otp_user_id');
        const userEmail = getCookie('otp_user_email');

        if (!userId || userEmail) {
            window.location.href = "{{ route('login') }}";
        }

        passwordForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            alertContainer.innerHTML = '';

            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (password !== confirmPassword) {
                showAlert("Password dan konfirmasi tidak cocok.");
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = "Mengirim...";

            const apiKey = document.querySelector('meta[name="api-key"]').content;

            try {
                const response = await fetch("{{ url('/api/v1/users/password') }}", {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': apiKey
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        password: password
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showAlert(data.message || "Password berhasil diubah!", "success");

                    // Hapus cookie
                    deleteCookie('otp_user_id');
                    deleteCookie('otp_user_email');
                    deleteCookie('otp_expires_at');

                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}";
                    }, 2000);
                } else {
                    showAlert(data.message || "Terjadi kesalahan saat mengganti password.");
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert("Gagal terhubung ke server. Coba lagi nanti.");
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = "Ganti Password";
            }
        });

        // Toggle show/hide password
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

        togglePassword?.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Show' : 'Hide';
        });

        toggleConfirmPassword?.addEventListener('click', function () {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Show' : 'Hide';
        });
    });
</script>
</body>
</html>
