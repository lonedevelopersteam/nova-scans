<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Admin - {{ env('APP_NAME', 'Default Title') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="{{ asset('icon.png') }}" type="image/x-icon">
    <meta name="api-key" content="{{ env('API_KEY') }}">
    <style>
        body {
            background-color: #f3f4f6; /* Light gray background similar to the image */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .container {
            max-width: 600px; /* Lebar container disesuaikan */
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left; /* Konten di dalam form rata kiri */
        }
        .form-label {
            font-weight: 600; /* Font lebih tebal untuk label */
            margin-bottom: 0.25rem; /* Sedikit spasi di bawah label */
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        .form-text {
            color: #6c757d; /* Warna teks bantuan */
            font-size: 0.875em; /* Ukuran teks bantuan lebih kecil */
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
        .input-group-text {
            cursor: pointer; /* Menunjukkan bahwa ini dapat diklik */
        }
        .alert-container {
            margin-bottom: 1rem; /* Spasi di bawah pesan alert */
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4 text-center fs-4">Masukkan detail admin di bawah ini.</h1>

    <div id="alertContainer" class="alert-container">
    </div>

    <form id="registrationForm" method="POST">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="" autocomplete="off" required>
            <div id="emailHelp" class="form-text">Masukkan alamat email Anda.</div>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="" autocomplete="off" required>
            <div id="usernameHelp" class="form-text">Nama pengguna yang Anda inginkan.</div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" required>
                <button class="btn btn-outline-secondary input-group-text" type="button" id="togglePassword">Show</button>
            </div>
            <div id="passwordHelp" class="form-text">Buat kata sandi yang kuat.</div>
        </div>

        <div class="mb-4">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <button class="btn btn-outline-secondary input-group-text" type="button" id="toggleConfirmPassword">Show</button>
            </div>
            <div id="confirmPasswordHelp" class="form-text">Ketik ulang kata sandi Anda.</div>
        </div>

        <button type="submit" class="btn btn-submit" id="submitButton">Buat</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const registrationForm = document.getElementById('registrationForm');
        const submitButton = document.getElementById('submitButton');
        const alertContainer = document.getElementById('alertContainer');

        // Fungsi untuk menampilkan pesan alert
        function showAlert(message, type) {
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        }

        // Event listener untuk pengiriman formulir
        registrationForm.addEventListener('submit', async function(event) {
            event.preventDefault(); // Mencegah pengiriman formulir default

            // Nonaktifkan tombol dan tampilkan indikator loading (opsional)
            submitButton.disabled = true;
            submitButton.textContent = 'Membuat...';
            alertContainer.innerHTML = ''; // Hapus pesan alert sebelumnya

            const email = document.getElementById('email').value;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const csrfToken = document.querySelector('input[name="_token"]').value;

            if (password !== confirmPassword) {
                showAlert('Konfirmasi Kata Sandi tidak cocok.', 'danger');
                submitButton.disabled = false;
                submitButton.textContent = 'Buat';
                return;
            }

            try {
                const apiKey = document.querySelector('meta[name="api-key"]').content;
                const baseUrl = "{{ url('/') }}";
                const apiUrl = `${baseUrl}/api/v1/users/register-admin`;
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': apiKey,
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        email: email,
                        username: username,
                        password: password
                    })
                });

                const data = await response.json();

                if (response.ok) { // Status kode 2xx
                    if (data.success) {
                        showAlert(data.message || 'Admin berhasil didaftarkan.', 'success');
                        // --- Perubahan di sini: Reload halaman setelah beberapa saat ---
                        setTimeout(() => {
                            window.location.reload();
                        }, 500); // Reload setelah 2 detik agar pesan sukses terlihat
                        // registrationForm.reset(); // Anda bisa menghapus ini jika ingin reload penuh
                    } else {
                        // Ini akan menangani kasus seperti {"success": false, "message": "username already registered"}
                        showAlert(data.message || 'Terjadi kesalahan yang tidak diketahui.', 'danger');
                    }
                } else { // Status kode 4xx, 5xx
                    if (data.message) {
                        // Kasus seperti {"message": "username already registered"} atau validasi lainnya
                        showAlert(data.message, 'danger');
                    } else if (data.errors) {
                        // Laravel default validation errors {"errors": {"email": ["The email field is required."]}}
                        let errorMessages = '';
                        for (const key in data.errors) {
                            errorMessages += `<li>${data.errors[key].join(', ')}</li>`;
                        }
                        showAlert(`<ul>${errorMessages}</ul>`, 'danger');
                    } else {
                        showAlert('Terjadi kesalahan pada server. Silakan coba lagi.', 'danger');
                    }
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('Gagal terhubung ke server. Pastikan Anda memiliki koneksi internet.', 'danger');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Buat';
            }
        });

        // JavaScript untuk fungsi 'Show' password (tetap sama)
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        if (togglePassword && passwordField) {
            togglePassword.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.textContent = type === 'password' ? 'Show' : 'Hide';
            });
        }

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordField = document.getElementById('confirm_password');

        if (toggleConfirmPassword && confirmPasswordField) {
            toggleConfirmPassword.addEventListener('click', function() {
                const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordField.setAttribute('type', type);
                this.textContent = type === 'password' ? 'Show' : 'Hide';
            });
        }
    });
</script>
</body>
</html>
