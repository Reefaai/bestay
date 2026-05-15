# Bestay — Hotel Booking System

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=flat-square)](LICENSE)

Sistem reservasi hotel berbasis web yang dibangun dengan **Laravel 12**, **Tailwind CSS 4**, dan **Alpine.js**. Menyediakan fitur lengkap mulai dari pencarian kamar, booking, pembayaran, hingga panel admin dengan dashboard, monitoring payment, dan manajemen user.

> **Tugas Mata Kuliah:** Pemrograman Web Lanjut (PWL)

---

## Preview Poject

![Preview](docs/screenshots/preview.png)

---

## Fitur Utama

### Guest/User
- Pencarian & filter kamar (tipe, harga, kapasitas)
- Booking kamar dengan pengecekan ketersediaan otomatis
- Sistem pembayaran (Bank Transfer, E-Wallet, Credit Card)
- Dashboard riwayat booking & status pembayaran
- Notifikasi (booking dikonfirmasi, pembayaran berhasil, dll)
- Pembatalan booking dengan refund otomatis

### Admin
- **Dashboard** — statistik real-time, chart booking & revenue 6 bulan, distribusi status payment
- **Manajemen Kamar** — CRUD lengkap, soft-delete (tidak bisa hapus jika ada booking aktif)
- **Manajemen Booking** — konfirmasi, tolak, selesaikan, dengan payment history per booking
- **Deteksi Konflik** — deteksi otomatis booking yang overlap pada kamar & tanggal yang sama
- **Monitoring Payment** — filter, search, verifikasi, override status (paid/failed/refunded)
- **Manajemen User** — lihat semua user, riwayat booking per user

### Sistem
- Autentikasi lengkap (login, register, logout)
- Role-based access control (admin & user)
- Auto-expire pembayaran yang tidak diselesaikan (scheduler setiap 5 menit)
- Payment audit trail (immutable status log)
- REST API lengkap dengan Laravel Sanctum
- Responsive design (mobile-friendly)
- Dark mode support
- Siap deploy ke Railway (Nixpacks)

---

## Tech Stack

| Layer              | Teknologi                                    |
| --------------------| ----------------------------------------------|
| **Backend**        | PHP 8.2, Laravel 12                          |
| **Frontend**       | Blade Templates, Tailwind CSS 4, Alpine.js 3 |
| **Charts**         | Chart.js 4                                   |
| **Database**       | SQLite (development) / MySQL (production)    |
| **Authentication** | Laravel Sanctum (API tokens)                 |
| **Build Tool**     | Vite 7                                       |
| **Deployment**     | Railway via Nixpacks                         |

---

## Dokumentasi

| Dokumen | Deskripsi |
|---------|-----------|
| [Screenshots](docs/screenshots.md) | Dokumentasi visual semua halaman — publik, user, dan admin |
| [ERD — Entity Relationship Diagram](docs/erd.md) | Skema database lengkap, diagram Mermaid, relasi antar tabel, status transisi |
| [System Flow](docs/flow.md) | Alur booking, pembayaran, notifikasi, admin dashboard, scheduler — dengan diagram Mermaid |
| [API Endpoint][docs/api.md] | Dokumentasi jelas untuk semua API endpoint di project ini |

---

## Quick Start

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
type nul > database/database.sqlite   # Windows
php artisan migrate && php artisan db:seed
npm run build
composer dev
```

Buka **http://localhost:8000**

---

## Akun Demo

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@bestay.com` | `password` |
| **User** | `user@bestay.com` | `password` |
| **User** | `siti@bestay.com` | `password` |

---

## Prasyarat & Instalasi Lengkap

### Prasyarat

| Software | Versi Minimum |
| ----------| ---------------|
| PHP      | >= 8.2        |
| Composer | >= 2.x        |
| Node.js  | >= 18.x       |
| NPM      | >= 9.x        |

**PHP Extensions:** `pdo_sqlite`, `mbstring`, `xml`, `curl`, `bcmath`, `fileinfo`, `tokenizer`, `ctype`, `openssl`

### 1. Clone Repository

```bash
git clone https://github.com/reefai/bestay.git
cd bestay
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Setup Database

```bash
# Buat file database SQLite (Windows)
type nul > database/database.sqlite

# Linux/Mac
touch database/database.sqlite

php artisan migrate
php artisan db:seed
```

### 5. Build Frontend

```bash
npm run build
```

### 6. Jalankan Aplikasi

**Semua service sekaligus:**
```bash
composer dev
```

Menjalankan: Laravel server · Queue worker · Log viewer (Pail) · Vite HMR

**Manual:**
```bash
php artisan serve      # Terminal 1
npm run dev            # Terminal 2
php artisan queue:listen  # Terminal 3 (opsional)
```

---

## Struktur Project

```
bestay/
├── app/
│   ├── Console/Commands/        # ExpirePendingPayments
│   ├── Http/
│   │   ├── Controllers/         # API controllers (Sanctum)
│   │   │   └── Web/             # Web controllers (session)
│   │   │       ├── AdminDashboardController.php
│   │   │       ├── AdminBookingController.php
│   │   │       ├── AdminPaymentController.php
│   │   │       ├── AdminRoomController.php
│   │   │       └── AdminUserController.php
│   │   ├── Middleware/          # AdminMiddleware
│   │   └── Requests/            # Form Request validation
│   ├── Models/                  # User, Room, Booking, Payment, PaymentStatusLog, Notification
│   ├── Policies/                # BookingPolicy, PaymentPolicy, RoomPolicy
│   └── Services/
│       ├── BookingService.php
│       ├── PaymentService.php
│       ├── NotificationService.php
│       └── Payments/Exceptions/ # Custom exceptions
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── css/                     # Tailwind CSS + design tokens
│   ├── js/                      # Alpine.js
│   └── views/
│       ├── admin/               # Dashboard, bookings, payments, rooms, users
│       ├── auth/
│       ├── components/          # navbar, footer, status-badge, pagination
│       ├── dashboard/           # User dashboard
│       ├── layouts/             # app.blade.php, admin.blade.php
│       ├── payments/
│       └── rooms/
├── routes/
│   ├── api.php                  # REST API (Sanctum)
│   ├── web.php                  # Web routes (session)
│   └── console.php              # Scheduler
├── tests/
├── docs/                        # ERD, flow diagram, preview
├── nixpacks.toml
└── CONTRIBUTING.md
```

---

## Deployment

### Railway (Recommended)

Aplikasi sudah dikonfigurasi untuk deploy ke [Railway](https://railway.app) via Nixpacks (`nixpacks.toml`).

#### 1. Buat Project

- Push repo ke GitHub
- Railway: **New Project** → **Deploy from GitHub repo**
- Railway otomatis mendeteksi Nixpacks

#### 2. Setup Database

**Opsi A — SQLite:**
- Tambah **Volume** di Railway, mount ke `/app/storage` dan `/app/database`
- Set `DB_CONNECTION=sqlite`

**Opsi B — MySQL:**
- **Add Plugin** → **MySQL**
- Set `DB_CONNECTION=mysql`

#### 3. Environment Variables

| Variable | Value |
|----------|-------|
| `APP_KEY` | Jalankan `php artisan key:generate --show` lokal |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | URL Railway kamu |

#### 4. Scheduler

Railway tidak punya cron bawaan. Gunakan [cron-job.org](https://cron-job.org) (gratis) untuk trigger `payments:expire` setiap 5 menit, atau deploy instance kedua dengan start command `php artisan schedule:work`.

#### 5. Queue Worker

- **Sederhana:** Set `QUEUE_CONNECTION=sync`
- **Production:** Tambah service baru, start command: `php artisan queue:work --tries=3`

---

## Testing

```bash
# Semua test
php artisan test

# Via composer
composer test

# Dengan coverage
php artisan test --coverage

# Test spesifik
php artisan test --filter=TestName
```

---

## Kontribusi

Lihat [CONTRIBUTING.md](CONTRIBUTING.md) untuk panduan lengkap.

1. Fork repository
2. Buat branch: `git checkout -b feature/nama-fitur`
3. Commit: `git commit -m 'feat: deskripsi singkat'`
4. Push: `git push origin feature/nama-fitur`
5. Buat Pull Request

---

## Lisensi

[MIT License](LICENSE)

---

<p align="center">
  <sub>Built with ❤️ using Laravel 12 · Tailwind CSS 4 · Alpine.js · Chart.js</sub>
</p>
