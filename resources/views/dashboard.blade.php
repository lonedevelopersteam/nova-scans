<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Cosmic Scan</title>
    <link rel="icon" href="{{ asset('icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <meta name="api-key" content="{{ env('API_KEY') }}">

    <style>
        /* General Styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }

        /* Main Content & Header Styles */
        .main-container {
            margin-left: 250px;
            flex-grow: 1;
            transition: margin-left 0.3s ease;
        }

        .header {
            background-color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .hamburger-menu {
            background-color: transparent;
            color: #2c3e50;
            border: none;
            font-size: 24px;
            cursor: pointer;
            display: none;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .main-content {
            padding: 20px;
        }

        /* Dashboard Card Styles - Optimized for better UX */
        .dashboard-cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 20px;
            flex: 1 1 250px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }
        .card .icon-wrapper {
            background-color: #34495e;
            color: #fff;
            font-size: 24px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card-content {
            flex-grow: 1;
            text-align: center;
        }
        .card h4 {
            margin: 0;
            color: #34495e;
            font-size: 16px;
        }
        .card p {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }

        /* Tombol Bersihkan Data - Lebih Informatif */
        .cache-section {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 25px;
        }
        .cache-section h3 {
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .cache-section p.text-muted {
            font-size: 14px;
            margin-bottom: 20px;
            color: #666;
        }
        .btn-clear-cache {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }
        .btn-clear-cache:hover {
            background-color: #c0392b;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .hamburger-menu {
                display: block;
            }
        }
    </style>
</head>
<body>

@include('includes._sidebar')

<div class="main-container" id="main-container">
    <header class="header">
        <h1>Dashboard</h1>
    </header>

    <div class="main-content">
        <p>Selamat datang di halaman dashboard admin.</p>
        <div class="dashboard-cards">
            <div class="card">
                <div class="icon-wrapper"><i class="fas fa-user-shield"></i></div>
                <div class="card-content">
                    <h4>Jumlah Admin</h4>
                    <p id="totalAdmin">...</p>
                </div>
            </div>
            <div class="card">
                <div class="icon-wrapper"><i class="fas fa-user-edit"></i></div>
                <div class="card-content">
                    <h4>Jumlah Editor</h4>
                    <p id="totalEditor">...</p>
                </div>
            </div>
            <div class="card">
                <div class="icon-wrapper"><i class="fas fa-user"></i></div>
                <div class="card-content">
                    <h4>Jumlah Reader</h4>
                    <p id="totalReader">...</p>
                </div>
            </div>
        </div>

        <div class="mt-4 cache-section">
            <div id="alertContainer" class="mb-3"></div>
            <h3>Manajemen Cache Aplikasi</h3>
            <p class="text-muted">Tombol ini akan menghapus seluruh data cache utama aplikasi. Gunakan untuk memastikan konten terbaru muncul di situs.</p>
            <button id="clearCacheBtn" class="btn-clear-cache">Bersihkan Data</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    const clearCacheBtn = document.getElementById('clearCacheBtn');
    const alertContainer = document.getElementById('alertContainer');

    // Elemen untuk menampilkan total user
    const totalAdminEl = document.getElementById('totalAdmin');
    const totalEditorEl = document.getElementById('totalEditor');
    const totalReaderEl = document.getElementById('totalReader');

    // Fungsi untuk mendapatkan nilai dari cookie
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return decodeURIComponent(parts.pop().split(';').shift());
        }
        return null;
    }

    // Fungsi untuk menampilkan alert
    function showAlert(message, type = 'success') {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    // Fungsi untuk mengambil data total user dari API
    async function fetchTotalUsers() {
        // Tampilkan "Loading..." sebelum mengambil data
        totalAdminEl.textContent = '...';
        totalEditorEl.textContent = '...';
        totalReaderEl.textContent = '...';

        const usersLoginCookie = getCookie('users_login');
        if (!usersLoginCookie) {
            console.error('Cookie "users_login" not found. Cannot fetch user totals.');
            totalAdminEl.textContent = 'N/A';
            totalEditorEl.textContent = 'N/A';
            totalReaderEl.textContent = 'N/A';
            return;
        }

        try {
            const userData = JSON.parse(usersLoginCookie);
            const accessToken = userData.access_token;

            if (!accessToken) {
                console.error('Access token not found in cookie.');
                totalAdminEl.textContent = 'N/A';
                totalEditorEl.textContent = 'N/A';
                totalReaderEl.textContent = 'N/A';
                return;
            }

            const baseUrl = window.location.origin;
            const apiUrl = `${baseUrl}/api/v1/users/total`;

            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `${accessToken}`
                },
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Update elemen HTML dengan data dari API
                totalAdminEl.textContent = data.data.total_admin || 0;
                totalEditorEl.textContent = data.data.total_editor || 0;
                totalReaderEl.textContent = data.data.total_reader || 0;
            } else {
                console.error('Failed to fetch user totals:', data.message || 'Unknown error');
                totalAdminEl.textContent = 'N/A';
                totalEditorEl.textContent = 'N/A';
                totalReaderEl.textContent = 'N/A';
            }
        } catch (error) {
            console.error('Error fetching user totals:', error);
            totalAdminEl.textContent = 'Error';
            totalEditorEl.textContent = 'Error';
            totalReaderEl.textContent = 'Error';
        }
    }

    // Aksi tombol "Bersihkan Data"
    clearCacheBtn.addEventListener('click', async () => {
        if (!confirm('Apakah Anda yakin ingin membersihkan data cache? Tindakan ini akan menghapus semua cache yang digunakan aplikasi utama.')) {
            return;
        }

        const baseUrl = window.location.origin;
        const apiUrl = `${baseUrl}/api/v1/manga/clearCache`;
        const apiKey = document.querySelector('meta[name="api-key"]')?.content;
        const csrfToken = document.querySelector('input[name="_token"]')?.value || '';

        clearCacheBtn.disabled = true;
        clearCacheBtn.textContent = 'Memproses...';
        alertContainer.innerHTML = '';

        try {
            const response = await fetch(apiUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': apiKey,
                    'X-CSRF-TOKEN': csrfToken
                },
            });

            const data = await response.json();

            if (response.ok) {
                showAlert(data.message || 'Cache berhasil dibersihkan!');
            } else {
                showAlert(data.message || 'Gagal membersihkan cache.', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat memanggil API. Coba lagi.', 'danger');
        } finally {
            clearCacheBtn.disabled = false;
            clearCacheBtn.textContent = 'Bersihkan Data';

            // Perbarui data total pengguna setelah cache dibersihkan
            fetchTotalUsers();
        }
    });

    // Panggil fungsi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        fetchTotalUsers();
    });

    // Handle resize window
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.remove('sidebar-open');
            }
        }
    });
</script>

</body>
</html>
