<style>
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
    .sidebar a:hover, .sidebar a.active {
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

    /* Responsiveness */
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
    }
</style>

<div class="sidebar" id="sidebar">
    <button class="hamburger-menu" id="toggleSidebar">
        <i class="fas fa-bars"></i>
    </button>
    <h2>Cosmic Scan</h2>
    <a href="{{ url('/') }}" id="dashboard-link">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <a href="#" id="users-dropdown">
        <i class="fas fa-users"></i> Users <i class="fas fa-chevron-down dropdown-arrow"></i>
    </a>
    <div class="submenu" id="users-submenu">
        <a href="{{ url('users/admin') }}" id="admin-link">
            <i class="fas fa-user-shield"></i> Admin
        </a>
        <a href="{{ url('users/editor') }}" id="editor-link">
            <i class="fas fa-user-edit"></i> Editor
        </a>
        <a href="{{ url('users/reader') }}" id="reader-link">
            <i class="fas fa-user"></i> Reader
        </a>
    </div>
    <a href="{{ url('settings') }}" id="settings-link">
        <i class="fas fa-cog"></i> Settings
    </a>
    <a href="#" id="logout-link">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

<script>
    const toggleSidebarBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const usersDropdown = document.getElementById('users-dropdown');
    const usersSubmenu = document.getElementById('users-submenu');
    const logoutLink = document.getElementById('logout-link');

    // Fungsi untuk mengaktifkan tautan sidebar berdasarkan URL
    function setActiveLink() {
        const currentUrl = window.location.href;
        const navLinks = document.querySelectorAll('.sidebar a');

        // Nonaktifkan semua tautan terlebih dahulu
        navLinks.forEach(link => link.classList.remove('active'));

        // Cek tautan Dashboard
        if (currentUrl.endsWith('/') || currentUrl.includes('/dashboard')) {
            document.getElementById('dashboard-link').classList.add('active');
        }

        // Cek tautan Submenu Users
        if (currentUrl.includes('/users')) {
            usersDropdown.classList.add('active');
            usersSubmenu.classList.add('show');
            const arrow = usersDropdown.querySelector('.dropdown-arrow');
            arrow.classList.remove('fa-chevron-down');
            arrow.classList.add('fa-chevron-up');
        }

        // Cek tautan individual di Submenu Users
        if (currentUrl.includes('/users/admin')) {
            document.getElementById('admin-link').classList.add('active');
        } else if (currentUrl.includes('/users/editor')) {
            document.getElementById('editor-link').classList.add('active');
        } else if (currentUrl.includes('/users/reader')) {
            document.getElementById('reader-link').classList.add('active');
        }

        // Cek tautan Settings
        if (currentUrl.includes('/settings')) {
            document.getElementById('settings-link').classList.add('active');
        }
    }

    // Panggil fungsi saat DOM selesai dimuat
    document.addEventListener('DOMContentLoaded', setActiveLink);

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

    // Handle Logout: Hanya menghapus cookie
    logoutLink.addEventListener('click', (e) => {
        e.preventDefault();

        // Menghapus cookie users_login dengan menyetel tanggal kedaluwarsa di masa lalu
        document.cookie = 'users_login=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';

        // Mengalihkan pengguna ke halaman login
        window.location.href = '/login';
    });
</script>
