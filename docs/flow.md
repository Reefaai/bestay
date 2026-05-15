# System Flow

## Daftar Isi

1. [Booking Flow](#1-booking-flow)
2. [Payment Flow](#2-payment-flow)
3. [Notification Flow](#3-notification-flow)
4. [Admin Flow](#4-admin-flow)
5. [Scheduler Flow](#5-scheduler-flow)

---

## 1. Booking Flow

```mermaid
sequenceDiagram
    actor User
    participant Browser
    participant BookingController
    participant BookingService
    participant PaymentService
    participant DB

    User->>Browser: Pilih kamar & isi form booking
    Browser->>BookingController: POST /bookings (room_id, check_in, check_out)
    BookingController->>BookingController: Validasi StoreBookingRequest

    BookingController->>BookingService: createBooking(data, user)
    BookingService->>DB: BEGIN TRANSACTION

    BookingService->>DB: SELECT bookings WHERE room_id & overlap dates
    alt Ada konflik jadwal
        DB-->>BookingService: Bookings yang konflik
        BookingService-->>BookingController: throw RuntimeException (konflik)
        BookingController-->>Browser: 409 Conflict / redirect with error
    else Tidak ada konflik
        BookingService->>DB: INSERT bookings (status=pending)
        BookingService->>PaymentService: createForBooking(booking)
        PaymentService->>DB: INSERT payments (status=pending, expires_at=+60min)
        PaymentService->>DB: INSERT payment_status_logs (from=null, to=pending)
        BookingService->>DB: COMMIT
        BookingController-->>Browser: 201 Created / redirect ke payment page
    end
```

### Langkah-langkah

1. **User browse kamar** — publik, tidak perlu login
2. **User submit booking** (room_id, check_in, check_out, notes) — harus login
3. **Validasi** via `StoreBookingRequest`:
   - `room_id` ada di tabel rooms
   - `check_in >= hari ini`
   - `check_out > check_in`
   - `notes` maks 500 karakter (opsional)
4. **Service layer** `BookingService::createBooking()`:
   - Cek kamar aktif (`is_active = true`)
   - Cek tidak ada booking yang overlap (`getConflictingBookings()`)
   - Hitung `total_price = nights × room.price_per_night`
   - Buat booking status `pending`
   - Buat payment via `PaymentService::createForBooking()` (status `pending`, expiry 60 menit)
   - Semua dalam satu database transaction
5. **Response** — 201 Created atau 409 Conflict / 422 Unprocessable

---

## 2. Payment Flow

```mermaid
sequenceDiagram
    actor User
    participant Browser
    participant PaymentController
    participant PaymentService
    participant DB
    participant NotificationService

    User->>Browser: Pilih metode pembayaran
    Browser->>PaymentController: POST /payments/{id}/method
    PaymentController->>PaymentService: selectMethod(payment, method)
    PaymentService->>DB: UPDATE payments SET method=...
    PaymentController-->>Browser: Redirect ke halaman konfirmasi

    User->>Browser: Konfirmasi pembayaran
    Browser->>PaymentController: POST /payments/{id}/process {outcome: success|fail}
    PaymentController->>PaymentService: processOutcome(payment, outcome)

    PaymentService->>DB: Cek apakah payment sudah expired
    alt Payment expired
        PaymentService->>DB: UPDATE status=expired, INSERT log
        PaymentService->>DB: UPDATE booking status=cancelled (jika pending)
        PaymentService-->>PaymentController: throw PaymentExpiredException
        PaymentController-->>Browser: Redirect with error
    else outcome = success
        PaymentService->>DB: SELECT FOR UPDATE (row lock)
        PaymentService->>DB: UPDATE payments SET status=paid, paid_at=now()
        PaymentService->>DB: INSERT payment_status_logs
        PaymentService->>DB: UPDATE bookings SET status=confirmed
        PaymentService->>DB: COMMIT
        PaymentService->>NotificationService: sendPaymentSucceeded()
        NotificationService->>DB: INSERT notifications
        PaymentController-->>Browser: Redirect with success
    else outcome = fail
        PaymentService->>DB: SELECT FOR UPDATE (row lock)
        PaymentService->>DB: UPDATE payments SET status=failed, failure_reason=...
        PaymentService->>DB: INSERT payment_status_logs
        PaymentService->>DB: COMMIT
        PaymentService->>NotificationService: sendPaymentFailed()
        PaymentController-->>Browser: Redirect with info (bisa retry)
    end
```

### Status & Transisi

```
              ┌─────────┐
              │ PENDING │ ← expires in 60 min
              └────┬────┘
                   │
       ┌───────────┼───────────┐
       ▼           ▼           ▼
   ┌──────┐   ┌────────┐  ┌─────────┐
   │ PAID │   │ FAILED │  │ EXPIRED │
   └──┬───┘   └────────┘  └─────────┘
      │       (terminal)  (terminal)
      ▼
 ┌──────────┐
 │ REFUNDED │ ← hanya jika booking cancelled
 └──────────┘
 (terminal)
```

### Metode Pembayaran

| Metode | Key | Keterangan |
|--------|-----|------------|
| Bank Transfer | `bank_transfer` | Verifikasi manual oleh admin |
| E-Wallet | `e_wallet` | Simulasi otomatis |
| Credit Card | `credit_card` | Simulasi otomatis |

### Mekanisme Expiry

- Pending payment memiliki window expiry 60 menit (`PaymentService::EXPIRY_MINUTES`)
- Command `ExpirePendingPayments` berjalan setiap 5 menit via scheduler (`routes/console.php`)
- Command query semua payment `pending` dengan `expires_at < now()`, proses per chunk 100
- **Lazy expiry** juga terjadi saat user membuka halaman payment (`PaymentController::show()` memanggil `expireIfOverdue()`)
- Saat payment expired, booking terkait dibatalkan (jika masih `pending`)

### Mekanisme Retry

- Maks **5 percobaan pembayaran** per booking (`PaymentService::MAX_ATTEMPTS`)
- Hanya payment `failed` yang bisa di-retry
- Booking terkait harus masih berstatus `pending`
- Retry membuat **payment baru** (dengan expiry 60 menit baru)
- Pengecekan active payment menggunakan row-level lock (`SELECT ... FOR UPDATE`) untuk mencegah race condition

### Concurrency Safety

- Semua transisi status berjalan dalam `DB::transaction()`
- Row-level lock (`lockForUpdate()`) pada record payment memastikan akses serial
- `createForBooking()` juga mengunci payment yang ada untuk mencegah duplikasi active payment
- Partial unique index pada `payments(booking_id) WHERE status IN ('pending','paid')` (SQLite/Postgres)

---

## 3. Notification Flow

```mermaid
flowchart LR
    A[Event Terjadi] --> B[NotificationService]
    B --> C{Buat notifikasi}
    C -->|Berhasil| D[(notifications table)]
    C -->|Gagal| E[Log error]
    E --> F[Tidak re-throw]

    style E fill:#fee2e2
    style F fill:#fef9c3
```

> **Aturan penting:** Kegagalan notifikasi selalu di-catch dan di-log, **tidak pernah di-throw ulang**. Ini memastikan kegagalan notifikasi tidak membatalkan transaksi payment atau booking.

### Tipe Notifikasi

| Type | Trigger | Judul |
|------|---------|-------|
| `booking_confirmed` | Booking dikonfirmasi (via payment success atau admin) | Booking Dikonfirmasi |
| `booking_cancelled` | User membatalkan booking | Booking Dibatalkan |
| `status_updated` | Admin mengubah status booking | Status Booking Diperbarui |
| `payment_succeeded` | Pembayaran berhasil | Pembayaran Berhasil |
| `payment_failed` | Pembayaran gagal | Pembayaran Gagal |
| `payment_expired` | Pembayaran kedaluwarsa | Pembayaran Kedaluwarsa |
| `payment_refunded` | Pembayaran direfund | Pembayaran Direfund |

---

## 4. Admin Flow

### 4.1 Admin Dashboard

```mermaid
flowchart TD
    A[Admin Login] --> B["GET /admin"]
    B --> C[AdminDashboardController]
    C --> D[Kumpulkan statistik]

    D --> D1[Total bookings dan status]
    D --> D2[Total revenue dari paid payments]
    D --> D3[Jumlah user terdaftar]
    D --> D4[Jumlah kamar aktif]
    D --> D5[Hitung konflik booking]
    D --> D6[Monthly bookings 6 bulan terakhir]
    D --> D7[Monthly revenue 6 bulan terakhir]
    D --> D8[Distribusi status payment]
    D --> D9[5 booking terbaru]
    D --> D10[5 payment terbaru]

    D1 --> E[Render admin.dashboard]
    D2 --> E
    D3 --> E
    D4 --> E
    D5 --> E
    D6 --> E
    D7 --> E
    D8 --> E
    D9 --> E
    D10 --> E
    E --> F["Chart.js: Bar + Line + Donut"]
    E --> G[Alert banner jika ada konflik atau pending payment]
```

### 4.2 Booking Management

```mermaid
flowchart TD
    A["GET /admin/bookings"] --> B{Filter status?}
    B -->|Ya| C[Query WHERE status]
    B -->|Tidak| D[Query semua booking]
    C --> E[Paginate 15 per halaman]
    D --> E
    E --> F[Tampilkan tabel booking]

    F --> G[Klik View]
    G --> H["GET /admin/bookings/{id}"]
    H --> I[Detail booking + payment history]
    I --> J{Status booking?}
    J -->|pending| K["Tombol: Confirm / Cancel"]
    J -->|confirmed| L["Tombol: Complete / Cancel"]
    J -->|cancelled atau completed| M[Tidak ada aksi]

    K --> N["PATCH /admin/bookings/{id}/status"]
    L --> N
    N --> O["BookingService::updateStatus()"]
    O --> P{Transisi valid?}
    P -->|Ya| Q[UPDATE bookings]
    P -->|Tidak| R[Error: invalid transition]
```

### 4.3 Payment Monitoring & Verifikasi

```mermaid
flowchart TD
    A["GET /admin/payments"] --> B[Filter: status / method / search]
    B --> C[Paginate 20 per halaman]
    C --> D[Tampilkan tabel payment]

    D --> E[Klik View]
    E --> F["GET /admin/payments/{id}"]
    F --> G[Detail payment + status history timeline]
    G --> H{Status payment?}
    H -->|pending| I["Override: paid / failed"]
    H -->|"paid dan booking cancelled"| J[Override: refunded]
    H -->|terminal| K[Tidak ada aksi]

    I --> L["PATCH /admin/payments/{id}/status"]
    J --> L
    L --> M["PaymentService::adminOverride()"]
    M --> N[Validasi precondition]
    N --> O[SELECT FOR UPDATE]
    O --> Q[UPDATE payments + INSERT log]
    Q --> R[Kirim notifikasi]
```

### 4.4 User Management

```mermaid
flowchart TD
    A["GET /admin/users"] --> B[Filter: search / role]
    B --> C[Paginate 20 per halaman]
    C --> D["Tabel user + jumlah booking (withCount)"]

    D --> E[Klik View]
    E --> F["GET /admin/users/{id}"]
    F --> G[Profil user]
    F --> H[Riwayat booking lengkap]
    G --> I[Nama, email, role, tanggal daftar]
    H --> J[Daftar booking dengan status dan total harga]
```

### 4.5 Conflict Detection

```mermaid
flowchart TD
    A["GET /admin/bookings/conflicts"] --> B["Query booking WHERE status IN pending, confirmed"]
    B --> C["Eager load user + room"]
    C --> D[Group by room_id]
    D --> E{Jumlah booking per room}
    E -->|"kurang dari 2"| F[Skip room ini]
    E -->|"2 atau lebih"| G[Cek overlap antar pasangan]
    G --> H{"A.check_in < B.check_out\nDAN\nA.check_out > B.check_in"}
    H -->|Ya| I[Catat ID booking sebagai konflik]
    H -->|Tidak| J[Tidak konflik]
    I --> K[Kumpulkan semua ID unik]
    K --> L[Filter dari collection yang sudah di-load]
    L --> M[Group by room_id]
    M --> N[Tampilkan konflik per room]
```

### Access Control

| Layer | Mekanisme |
|-------|-----------|
| Web routes | `middleware(['auth', 'admin'])` + prefix `/admin` |
| API routes | `middleware('auth:sanctum')` + `middleware('admin')` + prefix `/api/admin` |
| `AdminMiddleware` | Cek `$user->role === 'admin'`, return 403 JSON (API) atau abort 403 (web) |
| Policies | Enforce admin-only untuk room create/update/delete |

---

## 5. Scheduler Flow

```mermaid
flowchart LR
    A[routes/console.php] -->|every 5 minutes| B[payments:expire command]
    B --> C[Query payments WHERE status=pending AND expires_at < now]
    C --> D[Chunk 100 records]
    D --> E[PaymentService::expireIfOverdue]
    E --> F[SELECT FOR UPDATE]
    F --> G[UPDATE status=expired]
    G --> H[INSERT payment_status_logs]
    H --> I{Booking masih pending?}
    I -->|Ya| J[UPDATE booking status=cancelled]
    I -->|Tidak| K[Skip]
    J & K --> L[sendPaymentExpired notification]
    L --> M[catch error, log, lanjut]
```

Didefinisikan di `routes/console.php`:

```php
Schedule::command('payments:expire')
    ->everyFiveMinutes()
    ->withoutOverlapping();
```

> `withoutOverlapping()` memastikan command tidak berjalan bersamaan jika eksekusi sebelumnya belum selesai.

---

## Ringkasan Alur Lengkap

```mermaid
flowchart TD
    U([User]) --> R[Browse Rooms]
    R --> B[Create Booking]
    B --> P[Payment Created - pending]
    P --> M[Select Method]
    M --> C[Confirm Payment]
    C --> S{Outcome}
    S -->|success| PAID[Payment PAID]
    S -->|fail| FAIL[Payment FAILED]
    PAID --> CONF[Booking CONFIRMED]
    FAIL --> RETRY{Retry?}
    RETRY -->|Ya, < 5x| P
    RETRY -->|Tidak| CANCEL[Booking stays pending]
    P -->|60 min timeout| EXP[Payment EXPIRED]
    EXP --> BCANCEL[Booking CANCELLED]

    CONF --> ADMIN([Admin])
    ADMIN --> COMPLETE[Mark Completed]
    ADMIN --> ACANCEL[Cancel Booking]
    ACANCEL --> REFUND[Payment REFUNDED]

    style PAID fill:#d1fae5
    style CONF fill:#d1fae5
    style COMPLETE fill:#d1fae5
    style FAIL fill:#fee2e2
    style EXP fill:#fee2e2
    style BCANCEL fill:#fee2e2
    style REFUND fill:#ede9fe
```
