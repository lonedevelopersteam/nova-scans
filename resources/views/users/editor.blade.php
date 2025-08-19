<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Editor - Cosmic Scan</title>
    <link rel="icon" href="{{ asset('icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../css/app.css"/>

    <meta name="api-key" content="{{ env('API_KEY') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Perubahan warna primary */
        .btn-primary, .input-group .btn-primary {
            background-color: #2c3e50;
            border-color: #2c3e50;
            color: #ecf0f1;
        }
        .btn-primary:hover, .input-group .btn-primary:hover {
            background-color: #34495e;
            border-color: #34495e;
        }
        .page-item.active .page-link {
            background-color: #2c3e50;
            border-color: #2c3e50;
            color: #ecf0f1;
        }
        .page-link {
            color: #2c3e50;
        }
        .fab {
            background-color: #2c3e50;
            color: #ecf0f1;
            border-color: #2c3e50;
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            font-size: 24px;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .fab:hover {
            background-color: #34495e;
            border-color: #34495e;
        }
        .fab:disabled {
            background-color: #95a5a6;
            border-color: #95a5a6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

@extends('includes._layout')

@section('header-title')
    <h1>Kelola Editor</h1>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="input-group" style="max-width: 300px;">
                <input type="text" class="form-control" placeholder="Search..." aria-label="Search" id="searchInput">
                <button class="btn btn-primary" type="button" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title" id="EditorCardTitle">Total Editor Terdaftar: <span id="totalEditorCount">Loading...</span></h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <nav class="mt-4" aria-label="Page navigation">
            <ul class="pagination justify-content-center">
            </ul>
        </nav>
    </div>

    <button type="button" class="btn btn-primary rounded-circle shadow-lg fab" id="addEditorButton">
        <i class="fas fa-plus"></i>
    </button>

    <div class="modal fade" id="unauthorizedModal" tabindex="-1" aria-labelledby="unauthorizedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unauthorizedModalLabel">Akses Ditolak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <p>Anda tidak memiliki akses untuk melakukan tindakan ini.</p>
                        <p><strong>Hanya Editor yang dapat mengelola data Editor.</strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data Editor ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addEditorModal" tabindex="-1" aria-labelledby="addEditorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="addEditorForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEditorModalLabel">Tambah Editor Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <input type="hidden" name="role" value="editor">
                        <input type="hidden" name="api_key" value="{{ env('API_KEY') }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const addEditorModal = new bootstrap.Modal(document.getElementById('addEditorModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const unauthorizedModal = new bootstrap.Modal(document.getElementById('unauthorizedModal'));
        const EditorTableBody = document.querySelector('table tbody');
        const paginationContainer = document.querySelector('.pagination');
        const addEditorForm = document.getElementById('addEditorForm');
        const addEditorButton = document.getElementById('addEditorButton');
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');

        const apiUrl = `/users`;
        const searchApiUrl = `/users/search`;
        let currentPage = 1;
        let currentUserData = null;
        let deleteUserId = null;
        let isSearchMode = false;
        let currentSearchQuery = '';

        // --- FUNGSI HELPER ---
        function getUserFromCookie() {
            const cookieValue = getCookie('users_login');
            if (!cookieValue) return null;
            try {
                return JSON.parse(cookieValue);
            } catch (e) {
                console.error('Error parsing user cookie:', e);
                return null;
            }
        }

        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }

        function isUserAdmin() {
            const userData = getUserFromCookie();
            return userData && userData.user && userData.user.role === 'Admin';
        }

        function showUnauthorizedModal() {
            unauthorizedModal.show();
        }

        function updateTotalEditorCount(total) {
            const totalCountElement = document.getElementById('totalEditorCount');
            if (totalCountElement) {
                totalCountElement.textContent = total;
            }
        }

        async function getFreshCsrfToken() {
            try {
                const response = await fetch('/sanctum/csrf-cookie', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to get new CSRF token.');
                }
                return document.querySelector('meta[name="csrf-token"]')?.content;
            } catch (error) {
                console.error('Error fetching new CSRF token:', error);
                return null;
            }
        }

        function displayValidationErrors(form, errors) {
            resetValidationErrors(form); // Reset dulu

            for (const field in errors) {
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    let feedback = input.nextElementSibling;
                    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                        feedback = document.createElement('div');
                        feedback.classList.add('invalid-feedback');
                        input.parentNode.insertBefore(feedback, input.nextSibling);
                    }
                    feedback.textContent = errors[field][0];
                }
            }
        }

        function resetValidationErrors(form) {
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        // --- FUNGSI PENGOLAHAN DATA & TAMPILAN ---
        async function searchEditorData(query) {
            if (!query.trim()) {
                isSearchMode = false;
                currentSearchQuery = '';
                fetchEditorData(1);
                return;
            }

            EditorTableBody.innerHTML = '<tr><td colspan="5" class="text-center">Searching...</td></tr>';
            isSearchMode = true;
            currentSearchQuery = query;

            try {
                const response = await fetch(`${searchApiUrl}?q=${encodeURIComponent(query)}&role=Editor`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                });

                const data = await response.json();

                if (response.status === 401) {
                    alert('Session expired. Please login again.');
                    window.location.href = '/login';
                    return;
                }

                if (response.ok && data.success) {
                    renderTable(data.data.data, true);
                    paginationContainer.innerHTML = '';
                    updateTotalEditorCount(data.data.data.length);
                } else {
                    throw new Error(data.message || 'Failed to search data');
                }
            } catch (error) {
                console.error('Search Error:', error);
                EditorTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error: ${error.message}</td></tr>`;
                paginationContainer.innerHTML = '';
                updateTotalEditorCount(0);
            }
        }

        async function fetchEditorData(page = 1) {
            EditorTableBody.innerHTML = '<tr><td colspan="5" class="text-center">Loading...</td></tr>';

            try {
                const response = await fetch(`${apiUrl}?role=Editor&page=${page}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                });

                const data = await response.json();

                if (response.status === 401) {
                    alert('Session expired. Please login again.');
                    window.location.href = '/login';
                    return;
                }

                if (response.ok && data.success) {
                    renderTable(data.data.data);
                    renderPagination(data.data.pagination);
                    updateTotalEditorCount(data.data.pagination.total);
                } else {
                    throw new Error(data.message || 'Failed to load data');
                }
            } catch (error) {
                console.error('Error:', error);
                EditorTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${error.message}</td></tr>`;
                paginationContainer.innerHTML = '';
                updateTotalEditorCount(0);
            }
        }

        function renderTable(users, isSearch = false) {
            EditorTableBody.innerHTML = '';
            if (users.length === 0) {
                const message = isSearch ? 'Tidak ada hasil pencarian.' : 'Tidak ada data editor.';
                EditorTableBody.innerHTML = `<tr><td colspan="5" class="text-center">${message}</td></tr>`;
                return;
            }

            const isAdmin = isUserAdmin();
            const currentUser = getUserFromCookie();
            const currentUserId = currentUser?.user?.id;

            users.forEach((user, index) => {
                const row = document.createElement('tr');
                const canDelete = isAdmin && user.id !== currentUserId;
                const deleteButtonClass = canDelete ? 'btn btn-danger btn-sm' : 'btn btn-danger btn-sm disabled';
                const deleteButtonTitle = !isAdmin ? 'Hanya Editor yang dapat menghapus' :
                    user.id === currentUserId ? 'Tidak dapat menghapus diri sendiri' :
                        'Hapus Editor';
                const rowNumber = isSearch ? index + 1 : (currentPage - 1) * 20 + index + 1;

                row.innerHTML = `
            <th scope="row">${rowNumber}</th>
            <td>${user.username}</td>
            <td>${user.email}</td>
            <td>${user.role}</td>
            <td>
                <button type="button"
                        class="${deleteButtonClass}"
                        data-user-id="${user.id}"
                        title="${deleteButtonTitle}"
                        ${!canDelete ? 'disabled' : ''}>
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </td>
            `;
                EditorTableBody.appendChild(row);
            });

            document.querySelectorAll('[data-user-id]:not(.disabled)').forEach(button => {
                button.addEventListener('click', function() {
                    if (!isUserAdmin()) {
                        showUnauthorizedModal();
                        return;
                    }
                    deleteUserId = this.getAttribute('data-user-id');
                    deleteModal.show();
                });
            });
        }

        function renderPagination(pagination) {
            paginationContainer.innerHTML = '';
            const { current_page, total_pages, prev_page_url, next_page_url } = pagination;

            const prevItem = document.createElement('li');
            prevItem.className = `page-item ${!prev_page_url ? 'disabled' : ''}`;
            prevItem.innerHTML = `<a class="page-link" href="#" data-page="${current_page - 1}" tabindex="-1" aria-disabled="${!prev_page_url}">Previous</a>`;
            paginationContainer.appendChild(prevItem);

            for (let i = 1; i <= total_pages; i++) {
                const pageItem = document.createElement('li');
                pageItem.className = `page-item ${current_page === i ? 'active' : ''}`;
                pageItem.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                paginationContainer.appendChild(pageItem);
            }

            const nextItem = document.createElement('li');
            nextItem.className = `page-item ${!next_page_url ? 'disabled' : ''}`;
            nextItem.innerHTML = `<a class="page-link" href="#" data-page="${current_page + 1}">Next</a>`;
            paginationContainer.appendChild(nextItem);
        }

        // --- EVENT LISTENERS ---
        searchButton.addEventListener('click', () => {
            const query = searchInput.value.trim();
            searchEditorData(query);
        });

        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchEditorData(searchInput.value.trim());
            }
        });

        searchInput.addEventListener('input', () => {
            if (searchInput.value.trim() === '' && isSearchMode) {
                isSearchMode = false;
                currentSearchQuery = '';
                fetchEditorData(1);
            }
        });

        paginationContainer.addEventListener('click', (e) => {
            e.preventDefault();
            const target = e.target;
            if (target.classList.contains('page-link')) {
                const page = parseInt(target.dataset.page);
                if (!isNaN(page) && !target.closest('.page-item').classList.contains('disabled')) {
                    currentPage = page;
                    fetchEditorData(currentPage);
                }
            }
        });

        addEditorButton.addEventListener('click', () => {
            if (!isUserAdmin()) {
                showUnauthorizedModal();
                return;
            }
            addEditorModal.show();
        });

        if (addEditorForm) {
            addEditorForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                if (!isUserAdmin()) {
                    showUnauthorizedModal();
                    return;
                }

                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.textContent;
                const form = this;

                resetValidationErrors(form);

                try {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Menyimpan...';

                    let response = await fetch(`/users`, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        }
                    });

                    if (response.status === 419) {
                        console.warn('CSRF token mismatch. Attempting to refresh token...');
                        const freshToken = await getFreshCsrfToken();
                        if (!freshToken) {
                            throw new Error('Could not refresh CSRF token.');
                        }
                        document.querySelector('meta[name="csrf-token"]').content = freshToken;
                        response = await fetch(`/users`, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': freshToken
                            }
                        });
                    }

                    const data = await response.json();

                    if (response.status === 422) {
                        displayValidationErrors(form, data.errors);
                        throw new Error('Formulir tidak valid. Silakan periksa kembali.');
                    }

                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal menyimpan data.');
                    }

                    alert('Data Editor berhasil disimpan!');
                    addEditorModal.hide();
                    form.reset();
                    if (isSearchMode) {
                        searchEditorData(currentSearchQuery);
                    } else {
                        fetchEditorData(currentPage);
                    }

                } catch (error) {
                    console.error('Error:', error);
                    // Hindari menampilkan alert jika errornya adalah validasi
                    if (!error.message.includes('Formulir tidak valid')) {
                        alert('Error: ' + error.message);
                    }
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            });
        }

        // --- LOGIKA DELETE YANG DIUPDATE ---
        confirmDeleteButton.addEventListener('click', async function() {
            if (!isUserAdmin()) {
                showUnauthorizedModal();
                return;
            }

            if (deleteUserId) {
                // Tampilkan loading state pada tombol
                const originalText = confirmDeleteButton.textContent;
                confirmDeleteButton.textContent = 'Menghapus...';
                confirmDeleteButton.disabled = true;

                try {
                    // Periksa apakah pengguna yang login tidak menghapus dirinya sendiri
                    const currentUser = getUserFromCookie();
                    if (currentUser && currentUser.user.id == deleteUserId) {
                        alert('Tidak dapat menghapus diri sendiri.');
                        return;
                    }

                    // Ambil token CSRF terbaru
                    const csrf_token = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (!csrf_token) {
                        throw new Error('CSRF token tidak ditemukan.');
                    }

                    // Kirim permintaan DELETE
                    let response = await fetch(`/users/${deleteUserId}?api_key=`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf_token
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        alert('Data Editor berhasil dihapus!');
                        deleteModal.hide();
                        // Muat ulang data setelah penghapusan berhasil
                        if (isSearchMode) {
                            searchEditorData(currentSearchQuery);
                        } else {
                            fetchEditorData(currentPage);
                        }
                    } else if (response.status === 403) {
                        // Tangani jika server mengembalikan 403 (Forbidden)
                        alert(data.message || 'Akses Ditolak. Anda tidak memiliki izin untuk tindakan ini.');
                    } else {
                        throw new Error(data.message || 'Gagal menghapus data.');
                    }

                } catch (error) {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                } finally {
                    deleteUserId = null;
                    confirmDeleteButton.textContent = originalText;
                    confirmDeleteButton.disabled = false;
                }
            }
        });

        // --- INISIALISASI ---
        function updateUIBasedOnRole() {
            const isAdmin = isUserAdmin();
            const userData = getUserFromCookie();

            if (!isAdmin) {
                addEditorButton.disabled = true;
                addEditorButton.title = 'Hanya Editor yang dapat menambah Editor baru';
                addEditorButton.style.opacity = '0.6';
                addEditorButton.style.cursor = 'not-allowed';
            } else {
                addEditorButton.disabled = false;
                addEditorButton.title = 'Tambah Editor Baru';
                addEditorButton.style.opacity = '1';
                addEditorButton.style.cursor = 'pointer';
            }
            console.log('Current user role:', userData?.user?.role);
            console.log('Is Editor:', isAdmin);
        }

        document.addEventListener('DOMContentLoaded', () => {
            currentUserData = getUserFromCookie();
            if (!currentUserData) {
                alert('Session not found. Please login again.');
                window.location.href = '/login';
                return;
            }
            updateUIBasedOnRole();
            fetchEditorData();
        });

        function checkTokenExpiry() {
            const userData = getUserFromCookie();
            if (!userData || !userData.access_token_expire) return false;
            const expireTime = new Date(userData.access_token_expire);
            const currentTime = new Date();
            if (currentTime >= expireTime) {
                alert('Session expired. Please login again.');
                window.location.href = '/login';
                return false;
            }
            return true;
        }

        setInterval(checkTokenExpiry, 5 * 60 * 1000);
    </script>
@endsection

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
