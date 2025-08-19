<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reader - Cosmic Scan</title>
    <link rel="icon" href="{{ asset('icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
    </style>
</head>
<body>

@extends('includes._layout')

@section('header-title')
    <h1>Kelola Reader</h1>
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
                <h5 class="card-title" id="ReaderCardTitle">Total Reader Terdaftar: <span id="totalReaderCount">Loading...</span></h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
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

    <script>
        const ReaderTableBody = document.querySelector('table tbody');
        const paginationContainer = document.querySelector('.pagination');
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');

        const apiUrl = `/users`;
        const searchApiUrl = `/users/search`;
        let currentPage = 1;
        let currentUserData = null;
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

        function updateTotalReaderCount(total) {
            const totalCountElement = document.getElementById('totalReaderCount');
            if (totalCountElement) {
                totalCountElement.textContent = total;
            }
        }

        // --- FUNGSI PENGOLAHAN DATA & TAMPILAN ---
        async function searchReaderData(query) {
            if (!query.trim()) {
                isSearchMode = false;
                currentSearchQuery = '';
                fetchReaderData(1);
                return;
            }

            ReaderTableBody.innerHTML = '<tr><td colspan="4" class="text-center">Searching...</td></tr>';
            isSearchMode = true;
            currentSearchQuery = query;

            try {
                const response = await fetch(`${searchApiUrl}?q=${encodeURIComponent(query)}&role=Reader`, {
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
                    updateTotalReaderCount(data.data.data.length);
                } else {
                    throw new Error(data.message || 'Failed to search data');
                }
            } catch (error) {
                console.error('Search Error:', error);
                ReaderTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Error: ${error.message}</td></tr>`;
                paginationContainer.innerHTML = '';
                updateTotalReaderCount(0);
            }
        }

        async function fetchReaderData(page = 1) {
            ReaderTableBody.innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';

            try {
                const response = await fetch(`${apiUrl}?role=Reader&page=${page}`, {
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
                    updateTotalReaderCount(data.data.pagination.total);
                } else {
                    throw new Error(data.message || 'Failed to load data');
                }
            } catch (error) {
                console.error('Error:', error);
                ReaderTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${error.message}</td></tr>`;
                paginationContainer.innerHTML = '';
                updateTotalReaderCount(0);
            }
        }

        function renderTable(users, isSearch = false) {
            ReaderTableBody.innerHTML = '';
            if (users.length === 0) {
                const message = isSearch ? 'Tidak ada hasil pencarian.' : 'Tidak ada data Reader.';
                ReaderTableBody.innerHTML = `<tr><td colspan="4" class="text-center">${message}</td></tr>`;
                return;
            }

            users.forEach((user, index) => {
                const row = document.createElement('tr');
                const rowNumber = isSearch ? index + 1 : (currentPage - 1) * 20 + index + 1;

                row.innerHTML = `
            <th scope="row">${rowNumber}</th>
            <td>${user.username}</td>
            <td>${user.email}</td>
            <td>${user.role}</td>
            `;
                ReaderTableBody.appendChild(row);
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
            searchReaderData(query);
        });

        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchReaderData(searchInput.value.trim());
            }
        });

        searchInput.addEventListener('input', () => {
            if (searchInput.value.trim() === '' && isSearchMode) {
                isSearchMode = false;
                currentSearchQuery = '';
                fetchReaderData(1);
            }
        });

        paginationContainer.addEventListener('click', (e) => {
            e.preventDefault();
            const target = e.target;
            if (target.classList.contains('page-link')) {
                const page = parseInt(target.dataset.page);
                if (!isNaN(page) && !target.closest('.page-item').classList.contains('disabled')) {
                    currentPage = page;
                    fetchReaderData(currentPage);
                }
            }
        });

        // --- INISIALISASI ---
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

        document.addEventListener('DOMContentLoaded', () => {
            currentUserData = getUserFromCookie();
            if (!currentUserData) {
                alert('Session not found. Please login again.');
                window.location.href = '/login';
                return;
            }
            fetchReaderData();
        });
    </script>
@endsection

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
