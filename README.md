<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Struktur Projek

Saya banyak berinteraksi di controller, middleware, request.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://i.imgur.com/8L1sYJL.png" width="400" alt="Laravel Logo"></a></p>

## ENV Wajib Setting
- **Settingan API KEY**
<p align="center"><a href="https://i.imgur.com/WE9neBf.png" target="_blank"><img src="https://i.imgur.com/WE9neBf.png" width="400" alt="Laravel Logo"></a></p>

- **Settingan DB internal dan WP MangaStream (mysql_read)**
<p align="center"><a href="https://i.imgur.com/VjtuJRL.png" target="_blank"><img src="https://i.imgur.com/VjtuJRL.png" width="400" alt="Laravel Logo"></a></p>

- **Settingan Redis dan SMTP**
<p align="center"><a href="https://i.imgur.com/urIvPqI.png" target="_blank"><img src="https://i.imgur.com/urIvPqI.png" width="400" alt="Laravel Logo"></a></p>

## Middleware API Header Authorization

Ada 2 middleware yang saya gunakan untuk keamanan api. 
- **Menggunakan nilai dari env API_KEY. Untuk semua akses api ini menggunakan API_KEY dari .env karena tidak memerlukan akses token login**
<p align="center"><a href="https://i.imgur.com/yT3N4qS.png" target="_blank"><img src="https://i.imgur.com/yT3N4qS.png" width="400" alt="Laravel Logo"></a></p>
<p align="center"><a href="https://i.imgur.com/KzwLgMN.png" target="_blank"><img src="https://i.imgur.com/KzwLgMN.png" width="600" alt="Laravel Logo"></a></p>
<p align="center"><a href="https://i.imgur.com/69815oc.png" target="_blank"><img src="https://i.imgur.com/69815oc.png" width="600" alt="Laravel Logo"></a></p>

- **Menggunakan nilai dari token login. Untuk semua akses api ini menggunakan token setelah login, jadi menggunakan token dari response login**
<p align="center"><a href="https://i.imgur.com/FsJ8Gb9.png" target="_blank"><img src="https://i.imgur.com/FsJ8Gb9.png" width="400" alt="Laravel Logo"></a></p>
<p align="center"><a href="https://i.imgur.com/1cQqldn.png" target="_blank"><img src="https://i.imgur.com/1cQqldn.png" width="600" alt="Laravel Logo"></a></p>
<p align="center"><a href="https://i.imgur.com/DYFIxhy.png" target="_blank"><img src="https://i.imgur.com/DYFIxhy.png" width="600" alt="Laravel Logo"></a></p>
