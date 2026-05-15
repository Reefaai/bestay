# API Documentation

Semua API menggunakan prefix `/api`. Autentikasi menggunakan **Laravel Sanctum** dengan Bearer Token.

Base URL: `http://localhost:8000`

---

## Authentication

Endpoint publik dengan rate limit 5 request/menit.

### POST /api/register

Registrasi akun user baru.

**Request body:**
```json
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

**Response 201:**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 2,
    "name": "Budi Santoso",
    "email": "budi@example.com",
    "role": "user"
  }
}
```

---

### POST /api/login

Login dan dapatkan Bearer Token.

**Request body:**
```json
{
  "email": "user@bestay.com",
  "password": "password"
}
```

**Response 200:**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 2,
    "name": "Budi Santoso",
    "email": "user@bestay.com",
    "role": "user"
  }
}
```

---

### POST /api/logout

Revoke token aktif. Membutuhkan autentikasi.

**Header:** `Authorization: Bearer {token}`

**Response 200:**
```json
{ "message": "Logged out successfully." }
```

---

### GET /api/profile

Lihat profil user yang sedang login.

**Header:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "id": 2,
  "name": "Budi Santoso",
  "email": "user@bestay.com",
  "role": "user"
}
```

---

## Rooms

### GET /api/rooms

Daftar semua kamar aktif. Publik, tidak perlu autentikasi.

**Query parameters:**

| Parameter   | Tipe   | Deskripsi                                            |
| -------------| --------| ------------------------------------------------------|
| `type`      | string | Filter tipe: `standard`, `deluxe`, `suite`, `family` |
| `min_price` | number | Harga minimum per malam                              |
| `max_price` | number | Harga maksimum per malam                             |
| `capacity`  | number | Kapasitas minimum tamu                               |

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Deluxe Ocean View",
      "type": "deluxe",
      "description": "...",
      "price_per_night": "750000.00",
      "capacity": 2,
      "image_url": "https://...",
      "is_active": true
    }
  ],
  "current_page": 1,
  "per_page": 15,
  "total": 8
}
```

---

### GET /api/rooms/{id}

Detail satu kamar. Publik.

**Response 200:**
```json
{
  "id": 1,
  "name": "Deluxe Ocean View",
  "type": "deluxe",
  "price_per_night": "750000.00",
  "capacity": 2,
  "is_active": true
}
```

---

### GET /api/rooms/{id}/availability

Cek ketersediaan kamar pada rentang tanggal tertentu. Membutuhkan autentikasi.

**Query parameters:**

| Parameter   | Tipe | Deskripsi            |
| -------------| ------| ----------------------|
| `check_in`  | date | Format: `YYYY-MM-DD` |
| `check_out` | date | Format: `YYYY-MM-DD` |

**Response 200:**
```json
{ "available": true }
```

---

### POST /api/rooms

Tambah kamar baru. Hanya admin.

**Header:** `Authorization: Bearer {token}`

**Request body:**
```json
{
  "name": "Suite Panorama",
  "type": "suite",
  "description": "Suite mewah dengan pemandangan panorama.",
  "price_per_night": 1500000,
  "capacity": 2,
  "image_url": "https://images.unsplash.com/..."
}
```

**Response 201:**
```json
{ "room": { "id": 9, "name": "Suite Panorama", ... } }
```

---

### PUT /api/rooms/{id}

Update data kamar. Hanya admin.

**Header:** `Authorization: Bearer {token}`

Body sama dengan POST, semua field opsional kecuali yang diubah.

---

### DELETE /api/rooms/{id}

Nonaktifkan kamar (soft-delete, set `is_active = false`). Hanya admin. Gagal jika ada booking aktif.

**Header:** `Authorization: Bearer {token}`

**Response 200:**
```json
{ "message": "Room deactivated successfully." }
```

**Response 409** (ada booking aktif):
```json
{ "message": "Cannot deactivate room with active bookings." }
```

---

## Bookings

Semua endpoint membutuhkan autentikasi.

**Header:** `Authorization: Bearer {token}`

### GET /api/bookings

Daftar booking milik user yang sedang login.

**Query parameters:** `status` (opsional): `pending`, `confirmed`, `cancelled`, `completed`

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "room_id": 1,
      "check_in": "2026-06-01",
      "check_out": "2026-06-03",
      "total_price": "1500000.00",
      "status": "confirmed",
      "notes": null,
      "room": { "id": 1, "name": "Deluxe Ocean View" }
    }
  ]
}
```

---

### POST /api/bookings

Buat booking baru. Sistem otomatis membuat payment dengan status `pending`.

**Request body:**
```json
{
  "room_id": 1,
  "check_in": "2026-06-01",
  "check_out": "2026-06-03",
  "notes": "Mohon siapkan extra pillow."
}
```

**Response 201:**
```json
{
  "booking": {
    "id": 5,
    "room_id": 1,
    "check_in": "2026-06-01",
    "check_out": "2026-06-03",
    "total_price": "1500000.00",
    "status": "pending"
  }
}
```

**Response 409** (kamar tidak tersedia):
```json
{
  "message": "Kamar tidak tersedia untuk tanggal yang dipilih",
  "conflicts": [
    { "id": 3, "check_in": "2026-05-30", "check_out": "2026-06-02", "status": "confirmed" }
  ]
}
```

---

### GET /api/bookings/{id}

Detail satu booking milik user.

---

### PATCH /api/bookings/{id}/cancel

Batalkan booking. Hanya bisa untuk booking berstatus `pending` atau `confirmed`. Payment aktif otomatis di-refund atau di-expire.

**Response 200:**
```json
{ "booking": { "id": 5, "status": "cancelled", ... } }
```

---

## Payments

Semua endpoint membutuhkan autentikasi.

**Header:** `Authorization: Bearer {token}`

### GET /api/payments

Daftar payment milik user yang sedang login.

---

### GET /api/payments/{id}

Detail satu payment.

**Response 200:**
```json
{
  "payment": {
    "id": 1,
    "booking_id": 5,
    "reference": "PAY-20260601-ABC123",
    "amount": "1500000.00",
    "method": null,
    "status": "pending",
    "expires_at": "2026-06-01T15:00:00Z"
  }
}
```

---

### POST /api/payments/{id}/method

Pilih metode pembayaran untuk payment yang masih `pending`.

**Request body:**
```json
{ "method": "bank_transfer" }
```

Nilai yang valid: `bank_transfer`, `e_wallet`, `credit_card`

---

### POST /api/payments/{id}/process

Proses pembayaran (simulasi outcome).

**Request body — sukses:**
```json
{ "outcome": "success" }
```

**Request body — gagal:**
```json
{
  "outcome": "fail",
  "failure_reason": "Saldo tidak mencukupi."
}
```

**Response 200 (success):** Payment menjadi `paid`, booking menjadi `confirmed`.

**Response 422** (payment sudah expired):
```json
{ "message": "Payment has expired." }
```

---

### POST /api/payments/{id}/retry

Buat ulang payment setelah gagal. Booking harus masih `pending`, maksimal 5 percobaan.

**Response 201:**
```json
{ "payment": { "id": 6, "status": "pending", "expires_at": "..." } }
```

---

## Notifications

Semua endpoint membutuhkan autentikasi.

**Header:** `Authorization: Bearer {token}`

### GET /api/notifications

Daftar notifikasi milik user.

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "type": "payment_succeeded",
      "title": "Pembayaran Berhasil",
      "message": "Pembayaran untuk Booking #5 berhasil.",
      "is_read": false,
      "created_at": "2026-06-01T14:05:00Z"
    }
  ]
}
```

---

### PATCH /api/notifications/{id}/read

Tandai satu notifikasi sebagai sudah dibaca.

---

### POST /api/notifications/read-all

Tandai semua notifikasi sebagai sudah dibaca.

---

## Admin Endpoints

Semua endpoint membutuhkan autentikasi admin.

**Header:** `Authorization: Bearer {token}` (akun dengan `role: admin`)

### GET /api/admin/bookings

Semua booking dengan filter opsional.

**Query parameters:** `status`, `user_id`, `room_id`

---

### GET /api/admin/bookings/conflicts

Daftar booking yang memiliki tanggal overlap pada kamar yang sama.

**Response 200:**
```json
{
  "conflicts": [
    {
      "id": 3,
      "room_id": 1,
      "check_in": "2026-05-30",
      "check_out": "2026-06-02",
      "status": "confirmed",
      "user": { "id": 2, "name": "Budi Santoso" },
      "room": { "id": 1, "name": "Deluxe Ocean View" }
    }
  ]
}
```

---

### PATCH /api/admin/bookings/{id}/status

Update status booking.

**Request body:**
```json
{ "status": "confirmed" }
```

Nilai yang valid: `confirmed`, `cancelled`, `completed`

**Response 422** (transisi tidak valid):
```json
{ "message": "Invalid status transition from 'completed' to 'confirmed'." }
```

---

### GET /api/admin/payments

Semua payment dengan filter opsional.

**Query parameters:** `status`, `method`, `booking_id`

---

### GET /api/admin/payments/{id}

Detail satu payment beserta booking terkait.

---

### PATCH /api/admin/payments/{id}/status

Override status payment sebagai admin.

**Request body:**
```json
{
  "status": "paid",
  "reason": "Konfirmasi manual via transfer bank."
}
```

Nilai yang valid: `paid`, `failed`, `refunded`

Aturan:
- `paid` — hanya jika status saat ini `pending`
- `failed` — hanya jika status saat ini `pending`
- `refunded` — hanya jika status saat ini `paid` dan booking sudah `cancelled`

**Response 409** (precondition tidak terpenuhi):
```json
{ "message": "Admin override to 'paid' requires current status to be 'pending'." }
```

---
