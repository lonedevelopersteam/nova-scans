<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - Cosmic Scan</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #ecf0f1;
            padding-top: 20px;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            transition: all 0.3s ease;
            transform: translateX(0);
            z-index: 1000;
        }

        .sidebar a {
            padding: 15px 20px;
            text-decoration: none;
            font-size: 16px;
            color: #ecf0f1;
            display: flex;
            align-items: center;
        }
        .sidebar a:hover, .sidebar .active {
            background-color: #34495e;
            color: #fff;
        }
        .sidebar a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }
        .sidebar h2 {
            text-align: center;
            color: #ecf0f1;
            margin-bottom: 30px;
        }
        .sidebar .submenu {
            padding-left: 30px;
            display: none;
        }
        .sidebar .submenu a {
            font-size: 14px;
            padding: 10px 20px;
        }
        .sidebar .submenu.show {
            display: block;
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

        /* Dashboard Card Styles */
        .dashboard-cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            flex: 1 1 250px;
        }
        .card h4 {
            margin-top: 0;
            color: #34495e;
        }
        .card p {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }
            .sidebar.sidebar-open {
                transform: translateX(0);
            }
            .main-container {
                margin-left: 0;
            }
            .hamburger-menu {
                display: block;
            }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <h2>Cosmic Scan</h2>
    <a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="#" id="users-dropdown"><i class="fas fa-users"></i> Users <i class="fas fa-chevron-down dropdown-arrow"></i></a>
    <div class="submenu" id="users-submenu">
        <a href="#"><i class="fas fa-user-shield"></i> Admin</a>
        <a href="#"><i class="fas fa-user-edit"></i> Editor</a>
        <a href="#"><i class="fas fa-user"></i> Reader</a>
    </div>
    <a href="#"><i class="fas fa-cog"></i> Settings</a>
    <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-container" id="main-container">
    <header class="header">
        <button class="hamburger-menu" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
        <h1>Dashboard</h1>
    </header>

    <div class="main-content">
        <p>Selamat datang di halaman dashboard admin.</p>
        <div class="dashboard-cards">
            <div class="card">
                <h4><i class="fas fa-user-shield"></i> Jumlah Admin</h4>
                <p>10</p>
            </div>
            <div class="card">
                <h4><i class="fas fa-user-edit"></i> Jumlah Editor</h4>
                <p>25</p>
            </div>
            <div class="card">
                <h4><i class="fas fa-user"></i> Jumlah Reader</h4>
                <p>1500</p>
            </div>
        </div>
    </div>
</div>

<script>
    const toggleSidebarBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContainer = document.getElementById('main-container');
    const usersDropdown = document.getElementById('users-dropdown');
    const usersSubmenu = document.getElementById('users-submenu');

    // Toggle Sidebar saat tombol hamburger diklik
    toggleSidebarBtn.addEventListener('click', () => {
        sidebar.classList.toggle('sidebar-open');
    });

    // Toggle Submenu Users
    usersDropdown.addEventListener('click', (e) => {
        e.preventDefault();
        usersSubmenu.classList.toggle('show');
        const arrow = usersDropdown.querySelector('.dropdown-arrow');
        arrow.classList.toggle('fa-chevron-down');
        arrow.classList.toggle('fa-chevron-up');
    });

    // Tutup sidebar ketika klik di luar area sidebar pada mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !toggleSidebarBtn.contains(e.target)) {
                sidebar.classList.remove('sidebar-open');
            }
        }
    });

    // Handle resize window
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('sidebar-open');
        }
    });
</script>

</body>
</html>
