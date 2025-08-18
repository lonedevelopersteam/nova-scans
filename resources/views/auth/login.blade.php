<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - {{ env('APP_NAME', 'Default Title') }}</title>
    <link rel="icon" href="{{ asset('icon.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Tambahkan meta api key -->
    <meta name="api-key" content="{{ env('API_KEY') }}">

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
        .btn-login {
            background-color: #0073aa;
            border-color: #0073aa;
            color: #fff;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 4px;
            width: 100%;
        }
        .btn-login:hover {
            background-color: #005a87;
            border-color: #005a87;
            color: #fff;
        }
        .login-text {
            color: #555;
            font-size: 14px;
            text-align: center;
        }
        .login-text a {
            color: #0073aa;
            text-decoration: none;
        }
        .login-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center mb-4">Login</h3>
    <div id="alertContainer"></div>

    <form action="#" id="loginForm" method="POST">
        @csrf
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="rememberMe">
            <label class="form-check-label" for="rememberMe">Ingat Saya</label>
        </div>

        <button type="submit" id="loginButton" class="btn btn-login mb-3">Log In</button>
    </form>

    <div class="login-text">
        <a href="{{ route('password') }}">Lupa Password?</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const alertContainer = document.getElementById('alertContainer');

        function showAlert(message, type = 'danger') {
            alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        }

        function setCookie(name, value, days) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "expires=" + date.toUTCString();
            } else {
                const date = new Date();
                date.setFullYear(date.getFullYear() + 100); // Menambahkan 100 tahun
                expires = "expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + JSON.stringify(value) + ";" + expires + ";path=/";
        }

        loginForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const username = usernameInput.value;
            const password = passwordInput.value;
            const rememberMe = document.getElementById('rememberMe').checked;
            const apiKey = document.querySelector('meta[name="api-key"]')?.content;

            if (!username || !password) {
                showAlert('Username dan Password harus diisi.');
                return;
            }

            loginButton.disabled = true;
            loginButton.textContent = 'Logging In...';
            alertContainer.innerHTML = '';

            try {
                const baseUrl = "{{ url('/') }}";
                const apiUrl = `${baseUrl}/api/v1/users/login-admin-editor`;
                const csrfToken = document.querySelector('input[name="_token"]')?.value || '';

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': apiKey,
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showAlert(data.message || 'Login berhasil!', 'success');

                    if (data.data) {
                        if (rememberMe) {
                            setCookie('users_login', data.data, 1/24);
                        } else {
                            setCookie('users_login', data.data, 1/24);
                        }
                    }

                    setTimeout(() => {
                        window.location.href = "{{ route('dashboard') }}";
                    }, 1500);

                } else {
                    // Tangani pesan error spesifik dari API
                    if (data.message === 'access denied: role not authorized') {
                        showAlert('Akses ditolak: Anda tidak memiliki izin.');
                    } else if (data.message === 'password wrong') {
                        showAlert('Password yang Anda masukkan salah.');
                    } else {
                        showAlert(data.message || 'Terjadi kesalahan. Mohon coba lagi.', 'danger');
                    }
                }

            } catch (error) {
                console.error('Login Error:', error);
                showAlert('Terjadi kesalahan saat login. Mohon coba lagi.', 'danger');

            } finally {
                loginButton.disabled = false;
                loginButton.textContent = 'Log In';
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
