# Bestay - Hotel Booking System

Sistem reservasi hotel berbasis web yang dibangun dengan Laravel 12. Mendukung manajemen kamar, booking, pembayaran, dan notifikasi untuk tamu maupun admin.

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 12
- **Frontend:** Blade, Tailwind CSS 4, Alpine.js
- **Build Tool:** Vite
- **Database:** SQLite (default)
- **Auth:** Laravel Sanctum

## Fitur

- Autentikasi (register, login, logout) dengan role admin & user
- Manajemen kamar (CRUD, tipe: standard/deluxe/suite/family)
- Booking kamar dengan validasi ketersediaan
- Sistem pembayaran (bank transfer, e-wallet, credit card)
- Status tracking pembayaran (pending → paid/failed/expired)
- Notifikasi real-time untuk user
- Dashboard admin untuk kelola booking & pembayaran
- API endpoint + Web interface

## Persyaratan

- PHP >= 8.2
- Composer
- Node.js >= 18
- NPM

## Instalasi

```bash
# Clone repository
git clone https://github.com/Reefaai/bestay.git
cd bestay

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Buat database SQLite
touch database/database.sqlite

# Jalankan migrasi & seeder
php artisan migrate --seed

# Build assets
npm run build
```

## Menjalankan Aplikasi

```bash
# Development (server + vite + queue)
composer dev

# Atau jalankan manual:
php artisan serve
npm run dev
```

Akses aplikasi di `http://localhost:8000`

## Akun Demo

| Role  | Email             | Password |
|-------|-------------------|----------|
| Admin | admin@bestay.com  | password |
| User  | user@bestay.com   | password |
| User  | siti@bestay.com   | password |

## Struktur Project

```
app/
├── Console/Commands/     # Artisan commands (expire payments)
├── Http/
│   ├── Controllers/      # API controllers
│   ├── Controllers/Web/  # Web controllers
│   ├── Middleware/        # Admin middleware
│   └── Requests/         # Form request validation
├── Models/               # Eloquent models
├── Policies/             # Authorization policies
├── Providers/            # Service providers
└── Services/             # Business logic layer
```

## API Endpoints

Aplikasi menyediakan REST API dengan autentikasi Sanctum:

- `POST /api/register` - Register
- `POST /api/login` - Login
- `GET /api/rooms` - Daftar kamar
- `POST /api/bookings` - Buat booking
- `GET /api/bookings` - Daftar booking user
- `POST /api/payments/{booking}/select-method` - Pilih metode bayar
- `POST /api/payments/{booking}/process` - Proses pembayaran
- `GET /api/notifications` - Daftar notifikasi

## Testing

```bash
composer test
```

## License

MIT
