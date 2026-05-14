# 🏨 Bestay - Hotel Booking System

Sistem reservasi hotel berbasis web yang dibangun dengan Laravel 12, Tailwind CSS 4, dan Alpine.js. Aplikasi ini menyediakan fitur lengkap mulai dari pencarian kamar, booking, pembayaran, hingga panel admin untuk manajemen hotel.

> **Tugas Mata Kuliah:** Pemrograman Web Lanjut (PWL)

## 📸 Screenshots

| Halaman Utama | Daftar Kamar | Detail Kamar |
|:---:|:---:|:---:|
| ![Home](docs/screenshots/home.png) | ![Rooms](docs/screenshots/rooms.png) | ![Room Detail](docs/screenshots/room-detail.png) |

| Dashboard User | Pembayaran | Admin Panel |
|:---:|:---:|:---:|
| ![Dashboard](docs/screenshots/dashboard.png) | ![Payment](docs/screenshots/payment.png) | ![Admin](docs/screenshots/admin.png) |

## ✨ Fitur

### Untuk Tamu (Guest/User)
- 🔍 Pencarian & filter kamar (tipe, harga, kapasitas)
- 📅 Booking kamar dengan pengecekan ketersediaan otomatis
- 💳 Sistem pembayaran (Bank Transfer, E-Wallet, Credit Card)
- 📊 Dashboard untuk melihat riwayat booking
- 🔔 Notifikasi real-time (booking dikonfirmasi, pembayaran berhasil, dll)
- ❌ Pembatalan booking

### Untuk Admin
- 🏠 Manajemen kamar (CRUD)
- 📋 Manajemen booking (konfirmasi, tolak, selesaikan)
- ⚠️ Deteksi konflik jadwal booking
- 💰 Monitoring pembayaran

### Sistem
- 🔐 Autentikasi (login, register, logout)
- 🛡️ Role-based access control (admin & user)
- ⏰ Auto-expire pembayaran yang tidak diselesaikan
- 📝 Payment status logging (audit trail)
- 🌐 REST API dengan Laravel Sanctum
- 📱 Responsive design (mobile-friendly)

## 🛠️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8.2, Laravel 12 |
| Frontend | Blade, Tailwind CSS 4, Alpine.js |
| Database | SQLite (dev) / MySQL (prod) |
| Auth API | Laravel Sanctum |
| Build Tool | Vite 7 |
| Deployment | Railway (Nixpacks) |

## 📋 Prasyarat

Pastikan sudah terinstall di komputer kamu:

- **PHP** >= 8.2 (dengan extension: pdo_sqlite, mbstring, xml, curl, bcmath, fileinfo)
- **Composer** >= 2.x
- **Node.js** >= 18.x
- **NPM** >= 9.x
- **Git**

## 🚀 Instalasi & Setup

### 1. Clone Repository

```bash
git clone https://github.com/USERNAME/bestay.git
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

Aplikasi menggunakan SQLite secara default (tidak perlu install MySQL):

```bash
# Buat file database SQLite
touch database/database.sqlite

# Jalankan migrasi
php artisan migrate

# (Opsional) Isi data dummy untuk testing
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

Ini akan menjalankan:
- Laravel server di `http://localhost:8000`
- Queue worker
- Log viewer (Pail)
- Vite dev server (hot reload)

**Atau jalankan manual satu per satu:**

```bash
# Terminal 1 - Laravel server
php artisan serve

# Terminal 2 - Vite (untuk hot reload CSS/JS)
npm run dev

# Terminal 3 - Queue worker (untuk notifikasi)
php artisan queue:listen
```

### 7. Akses Aplikasi

Buka browser dan akses `http://localhost:8000`

## 👤 Akun Demo

Setelah menjalankan `php artisan db:seed`, kamu bisa login dengan akun berikut:

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@bestay.com` | `password` |
| User | `user@bestay.com` | `password` |
| User | `siti@bestay.com` | `password` |

## 📁 Struktur Project

```
bestay/
├── app/
│   ├── Console/Commands/     # Artisan commands (expire payments)
│   ├── Http/
│   │   ├── Controllers/      # API controllers
│   │   │   └── Web/          # Web (Blade) controllers
│   │   ├── Middleware/       # Admin middleware
│   │   └── Requests/        # Form request validation
│   ├── Models/               # Eloquent models
│   ├── Policies/             # Authorization policies
│   ├── Providers/            # Service providers
│   └── Services/             # Business logic layer
│       └── Payments/         # Payment service & exceptions
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database schema
│   └── seeders/              # Demo data seeder
├── resources/
│   ├── css/                  # Tailwind CSS
│   ├── js/                   # Alpine.js
│   └── views/               # Blade templates
│       ├── admin/            # Admin panel views
│       ├── auth/             # Login & register
│       ├── components/       # Reusable components
│       ├── dashboard/        # User dashboard
│       ├── layouts/          # Base layout
│       ├── payments/         # Payment flow
│       └── rooms/            # Room listing & detail
├── routes/
│   ├── api.php              # REST API routes
│   └── web.php              # Web routes
├── tests/                    # PHPUnit tests
├── nixpacks.toml            # Railway deployment config
└── composer.json            # PHP dependencies
```

## 🔌 API Documentation

Aplikasi menyediakan REST API lengkap dengan autentikasi Sanctum.

### Authentication

```bash
# Register
POST /api/register
Body: { "name", "email", "password", "password_confirmation" }

# Login
POST /api/login
Body: { "email", "password" }
Response: { "token": "..." }

# Logout (requires token)
POST /api/logout
Header: Authorization: Bearer {token}
```

### Rooms (Public)

```bash
# List rooms
GET /api/rooms

# Room detail
GET /api/rooms/{id}

# Check availability (requires auth)
GET /api/rooms/{id}/availability?check_in=2025-01-01&check_out=2025-01-03
```

### Bookings (Requires Auth)

```bash
# List my bookings
GET /api/bookings

# Create booking
POST /api/bookings
Body: { "room_id", "check_in", "check_out", "notes" }

# View booking detail
GET /api/bookings/{id}

# Cancel booking
PATCH /api/bookings/{id}/cancel
```

### Payments (Requires Auth)

```bash
# List my payments
GET /api/payments

# View payment detail
GET /api/payments/{id}

# Select payment method
POST /api/payments/{id}/method
Body: { "method": "bank_transfer|e_wallet|credit_card" }

# Process payment
POST /api/payments/{id}/process

# Retry failed payment
POST /api/payments/{id}/retry
```

### Admin (Requires Auth + Admin Role)

```bash
# Bookings management
GET    /api/admin/bookings
GET    /api/admin/bookings/conflicts
GET    /api/admin/bookings/{id}
PATCH  /api/admin/bookings/{id}/status

# Payments management
GET    /api/admin/payments
GET    /api/admin/payments/{id}
PATCH  /api/admin/payments/{id}/status
```

## 🧪 Testing

```bash
# Jalankan semua test
composer test

# Atau langsung
php artisan test

# Dengan coverage
php artisan test --coverage
```

## 🚢 Deployment (Railway)

Aplikasi sudah dikonfigurasi untuk deploy ke [Railway](https://railway.app):

1. Push repo ke GitHub
2. Connect repo di Railway dashboard
3. Set environment variables:
   - `APP_KEY` (generate dengan `php artisan key:generate --show`)
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `DB_CONNECTION=sqlite`
4. Deploy otomatis via Nixpacks

Konfigurasi deployment ada di `nixpacks.toml`.

## 🤝 Kontribusi

1. Fork repository ini
2. Buat branch fitur baru (`git checkout -b feature/fitur-baru`)
3. Commit perubahan (`git commit -m 'feat: tambah fitur baru'`)
4. Push ke branch (`git push origin feature/fitur-baru`)
5. Buat Pull Request

## 📄 Lisensi

Project ini menggunakan lisensi [MIT](LICENSE).

## 👨‍💻 Author

**Reefai** — Pemrograman Web Lanjut (PWL)

---

<p align="center">
  Built with ❤️ using Laravel 12
</p>
