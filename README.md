# 🏨 Bestay — Hotel Booking System

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=flat-square)](LICENSE)

Sistem reservasi hotel berbasis web yang dibangun dengan **Laravel 12**, **Tailwind CSS 4**, dan **Alpine.js**. Aplikasi ini menyediakan fitur lengkap mulai dari pencarian kamar, booking, pembayaran, hingga panel admin untuk manajemen hotel.

> **📚 Tugas Mata Kuliah:** Pemrograman Web Lanjut (PWL)

---

## 📸 Screenshots

| Halaman Utama | Daftar Kamar | Detail Kamar |
|:---:|:---:|:---:|
| ![Home](docs/screenshots/home.png) | ![Rooms](docs/screenshots/rooms.png) | ![Room Detail](docs/screenshots/room-detail.png) |

| Dashboard User | Pembayaran | Admin Panel |
|:---:|:---:|:---:|
| ![Dashboard](docs/screenshots/dashboard.png) | ![Payment](docs/screenshots/payment.png) | ![Admin](docs/screenshots/admin.png) |

---

## ✨ Fitur Utama

### 👤 Untuk Tamu (Guest/User)
- 🔍 Pencarian & filter kamar (tipe, harga, kapasitas)
- 📅 Booking kamar dengan pengecekan ketersediaan otomatis
- 💳 Sistem pembayaran (Bank Transfer, E-Wallet, Credit Card)
- 📊 Dashboard untuk melihat riwayat booking & status pembayaran
- 🔔 Notifikasi real-time (booking dikonfirmasi, pembayaran berhasil, dll)
- ❌ Pembatalan booking

### 🛡️ Untuk Admin
- 🏠 Manajemen kamar (CRUD lengkap dengan upload gambar)
- 📋 Manajemen booking (konfirmasi, tolak, selesaikan)
- ⚠️ Deteksi konflik jadwal booking
- 💰 Monitoring & verifikasi pembayaran

### ⚙️ Sistem
- 🔐 Autentikasi lengkap (login, register, logout)
- 🛡️ Role-based access control (admin & user)
- ⏰ Auto-expire pembayaran yang tidak diselesaikan (via scheduler)
- 📝 Payment status logging / audit trail
- 🌐 REST API lengkap dengan Laravel Sanctum
- 📱 Responsive design (mobile-friendly)
- 🚀 Siap deploy ke Railway (Nixpacks)

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | PHP 8.2, Laravel 12 |
| **Frontend** | Blade Templates, Tailwind CSS 4, Alpine.js 3 |
| **Database** | SQLite (development) / MySQL (production) |
| **Authentication** | Laravel Sanctum (API tokens) |
| **Build Tool** | Vite 7 |
| **Deployment** | Railway via Nixpacks |

---

## 📖 Dokumentasi

| Dokumen | Deskripsi |
|---------|-----------|
| [ERD — Entity Relationship Diagram](docs/erd.md) | Skema database lengkap, relasi antar tabel, status transisi |
| [System Flow](docs/flow.md) | Alur booking, pembayaran, notifikasi, admin, dan scheduler |

---

## 📋 Prasyarat

Pastikan sudah terinstall:

| Software | Versi Minimum |
|----------|---------------|
| PHP | >= 8.2 |
| Composer | >= 2.x |
| Node.js | >= 18.x |
| NPM | >= 9.x |
| Git | latest |

**PHP Extensions yang dibutuhkan:** `pdo_sqlite`, `mbstring`, `xml`, `curl`, `bcmath`, `fileinfo`, `tokenizer`, `ctype`, `openssl`

---

## 🚀 Instalasi & Setup

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

Aplikasi menggunakan **SQLite** secara default — tidak perlu install MySQL untuk development:

```bash
# Buat file database SQLite (Windows)
type nul > database/database.sqlite

# Atau di Linux/Mac
touch database/database.sqlite

# Jalankan migrasi
php artisan migrate

# Isi data demo untuk testing
php artisan db:seed
```

### 5. Build Frontend Assets

```bash
npm run build
```

### 6. Jalankan Aplikasi

**Cara cepat (semua service sekaligus):**

```bash
composer dev
```

Perintah ini menjalankan secara bersamaan:
- 🌐 Laravel server → `http://localhost:8000`
- 📨 Queue worker (untuk notifikasi async)
- 📋 Log viewer (Pail)
- ⚡ Vite dev server (hot reload CSS/JS)

**Atau jalankan manual satu per satu:**

```bash
# Terminal 1 — Laravel server
php artisan serve

# Terminal 2 — Vite (hot reload)
npm run dev

# Terminal 3 — Queue worker (opsional, untuk notifikasi)
php artisan queue:listen
```

### 7. Akses Aplikasi

Buka browser: **http://localhost:8000**

---

## 👤 Akun Demo

Setelah menjalankan `php artisan db:seed`, gunakan akun berikut:

| Role | Nama | Email | Password |
|------|------|-------|----------|
| **Admin** | Admin Bestay | `admin@bestay.com` | `password` |
| **User** | Budi Santoso | `user@bestay.com` | `password` |
| **User** | Siti Rahayu | `siti@bestay.com` | `password` |

> ⚠️ Seeder bersifat idempotent — jika data sudah ada, `db:seed` tidak akan menduplikasi data.

---

## 📁 Struktur Project

```
bestay/
├── app/
│   ├── Console/Commands/        # Artisan commands (ExpirePendingPayments)
│   ├── Http/
│   │   ├── Controllers/         # API controllers
│   │   │   └── Web/             # Web (Blade) controllers
│   │   ├── Middleware/          # Custom middleware (AdminMiddleware)
│   │   └── Requests/           # Form request validation classes
│   ├── Models/                  # Eloquent models (User, Room, Booking, Payment, etc.)
│   ├── Policies/                # Authorization policies
│   ├── Providers/               # Service providers
│   └── Services/                # Business logic layer
│       └── Payments/            # Payment service & custom exceptions
├── database/
│   ├── factories/               # Model factories (untuk testing)
│   ├── migrations/              # Database schema migrations
│   └── seeders/                 # Demo data seeder
├── resources/
│   ├── css/                     # Tailwind CSS source
│   ├── js/                      # Alpine.js & app scripts
│   └── views/                   # Blade templates
│       ├── admin/               # Admin panel views
│       ├── auth/                # Login & register pages
│       ├── components/          # Reusable Blade components
│       ├── dashboard/           # User dashboard
│       ├── layouts/             # Base layout templates
│       ├── payments/            # Payment flow pages
│       └── rooms/               # Room listing & detail
├── routes/
│   ├── api.php                  # REST API routes (Sanctum protected)
│   └── web.php                  # Web routes (session-based)
├── tests/                       # PHPUnit test suite
├── docs/                        # Dokumentasi (ERD, flow)
├── nixpacks.toml                # Railway deployment config
├── CONTRIBUTING.md              # Panduan kontribusi
└── LICENSE                      # MIT License
```

---

## 🔌 API Documentation

REST API lengkap dengan autentikasi **Laravel Sanctum** (Bearer Token).

### 🔑 Authentication

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `POST` | `/api/register` | Registrasi user baru |
| `POST` | `/api/login` | Login & dapatkan token |
| `POST` | `/api/logout` | Logout (revoke token) |
| `GET` | `/api/profile` | Lihat profil user |

**Contoh Login:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@bestay.com", "password": "password"}'
```

**Response:**
```json
{
  "token": "1|abc123...",
  "user": { "id": 2, "name": "Budi Santoso", "email": "user@bestay.com" }
}
```

### 🏨 Rooms

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| `GET` | `/api/rooms` | ❌ | Daftar semua kamar aktif |
| `GET` | `/api/rooms/{id}` | ❌ | Detail kamar |
| `GET` | `/api/rooms/{id}/availability` | ✅ | Cek ketersediaan kamar |
| `POST` | `/api/rooms` | ✅ Admin | Tambah kamar baru |
| `PUT` | `/api/rooms/{id}` | ✅ Admin | Update kamar |
| `DELETE` | `/api/rooms/{id}` | ✅ Admin | Hapus kamar |

### 📅 Bookings

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| `GET` | `/api/bookings` | ✅ | Daftar booking saya |
| `POST` | `/api/bookings` | ✅ | Buat booking baru |
| `GET` | `/api/bookings/{id}` | ✅ | Detail booking |
| `PATCH` | `/api/bookings/{id}/cancel` | ✅ | Batalkan booking |

### 💳 Payments

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| `GET` | `/api/payments` | ✅ | Daftar pembayaran saya |
| `GET` | `/api/payments/{id}` | ✅ | Detail pembayaran |
| `POST` | `/api/payments/{id}/method` | ✅ | Pilih metode bayar |
| `POST` | `/api/payments/{id}/process` | ✅ | Proses pembayaran |
| `POST` | `/api/payments/{id}/retry` | ✅ | Retry pembayaran gagal |

### 🔔 Notifications

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| `GET` | `/api/notifications` | ✅ | Daftar notifikasi |
| `PATCH` | `/api/notifications/{id}/read` | ✅ | Tandai sudah dibaca |
| `POST` | `/api/notifications/read-all` | ✅ | Tandai semua dibaca |

### 🛡️ Admin Endpoints

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| `GET` | `/api/admin/bookings` | ✅ Admin | Semua booking |
| `GET` | `/api/admin/bookings/conflicts` | ✅ Admin | Booking konflik |
| `GET` | `/api/admin/bookings/{id}` | ✅ Admin | Detail booking |
| `PATCH` | `/api/admin/bookings/{id}/status` | ✅ Admin | Update status booking |
| `GET` | `/api/admin/payments` | ✅ Admin | Semua pembayaran |
| `GET` | `/api/admin/payments/{id}` | ✅ Admin | Detail pembayaran |
| `PATCH` | `/api/admin/payments/{id}/status` | ✅ Admin | Update status pembayaran |

> 💡 Semua endpoint yang membutuhkan auth menggunakan header: `Authorization: Bearer {token}`

---

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test

# Atau via composer script
composer test

# Dengan coverage report
php artisan test --coverage
```

---

## 🚢 Deployment

### Railway (Recommended)

Aplikasi sudah dikonfigurasi untuk deploy ke [Railway](https://railway.app) menggunakan Nixpacks:

1. Push repository ke GitHub
2. Buat project baru di Railway → Connect GitHub repo
3. Set environment variables:

| Variable | Value |
|----------|-------|
| `APP_KEY` | Generate: `php artisan key:generate --show` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | URL Railway kamu |
| `DB_CONNECTION` | `sqlite` |

4. Deploy otomatis setiap push ke `main`

> Konfigurasi deployment ada di [`nixpacks.toml`](nixpacks.toml). Seeder berjalan otomatis saat pertama kali deploy.

---

## 🗄️ Database Schema

```
┌──────────┐     ┌──────────────┐     ┌──────────┐
│  users   │     │   bookings   │     │  rooms   │
├──────────┤     ├──────────────┤     ├──────────┤
│ id       │◄────│ user_id      │     │ id       │
│ name     │     │ room_id      │────►│ name     │
│ email    │     │ check_in     │     │ type     │
│ password │     │ check_out    │     │ price    │
│ role     │     │ total_price  │     │ capacity │
└──────────┘     │ status       │     │ is_active│
                 │ notes        │     └──────────┘
                 └──────┬───────┘
                        │
                 ┌──────▼───────┐     ┌─────────────────────┐
                 │   payments   │     │  payment_status_logs │
                 ├──────────────┤     ├─────────────────────┤
                 │ id           │◄────│ payment_id          │
                 │ booking_id   │     │ from_status         │
                 │ reference    │     │ to_status           │
                 │ amount       │     │ actor_user_id       │
                 │ method       │     │ actor_type          │
                 │ status       │     │ reason              │
                 │ paid_at      │     └─────────────────────┘
                 │ expires_at   │
                 └──────────────┘

                 ┌────────────────┐
                 │ notifications  │
                 ├────────────────┤
                 │ id             │
                 │ user_id        │
                 │ booking_id     │
                 │ type           │
                 │ title          │
                 │ message        │
                 │ is_read        │
                 └────────────────┘
```

---

## 🔄 Payment Flow

```
┌─────────┐    ┌─────────────┐    ┌──────────┐    ┌────────┐
│ Booking │───►│ Select      │───►│ Process  │───►│  Paid  │
│ Created │    │ Method      │    │ Payment  │    │   ✓    │
└─────────┘    └─────────────┘    └────┬─────┘    └────────┘
                                       │
                                       ▼
                                  ┌──────────┐    ┌─────────┐
                                  │  Failed  │───►│  Retry  │──► (kembali ke Process)
                                  └──────────┘    └─────────┘
                                       │
                                       ▼
                                  ┌──────────┐
                                  │ Expired  │ (auto, setelah timeout)
                                  └──────────┘
```

**Status pembayaran:** `pending` → `paid` | `failed` → `expired`

---

## 🤝 Kontribusi

Lihat [CONTRIBUTING.md](CONTRIBUTING.md) untuk panduan lengkap kontribusi.

**Quick start:**

1. Fork repository ini
2. Buat branch fitur baru: `git checkout -b feature/nama-fitur`
3. Commit perubahan: `git commit -m 'feat: deskripsi singkat'`
4. Push ke branch: `git push origin feature/nama-fitur`
5. Buat Pull Request

---

## 📄 Lisensi

Project ini dilisensikan di bawah [MIT License](LICENSE).

---

## 👨‍💻 Author

**Reefai** — Pemrograman Web Lanjut (PWL)

---

<p align="center">
  <sub>Built with ❤️ using Laravel 12 • Tailwind CSS 4 • Alpine.js</sub>
</p>
