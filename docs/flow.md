# System Flow

## 1. Booking Flow

```
User                     Browser/API              Controller              BookingService          Database
 │                          │                        │                        │                    │
 │  Cari & pilih kamar      │                        │                        │                    │
 ├─────────────────────────►│                        │                        │                    │
 │                          │  GET /rooms            │                        │                    │
 │                          ├───────────────────────►│                        │                    │
 │                          │                        │  Room::active()        │                    │
 │                          │                        ├───────────────────────────────────────────────► rooms
 │                          │                        │◄───────────────────────────────────────────────┘
 │                          │◄───────────────────────│                        │                    │
 │◄─────────────────────────│                        │                        │                    │
 │                          │                        │                        │                    │
 │  Isi form booking        │                        │                        │                    │
 │  (check_in, check_out)   │                        │                        │                    │
 ├─────────────────────────►│                        │                        │                    │
 │                          │  POST /bookings        │                        │                    │
 │                          ├───────────────────────►│                        │                    │
 │                          │                        │  StoreBookingRequest   │                    │
 │                          │                        │  (validasi)            │                    │
 │                          │                        │                        │                    │
 │                          │                        │  createBooking()       │                    │
 │                          │                        ├───────────────────────►│                    │
 │                          │                        │                        │                    │
 │                          │                        │                        │  BEGIN TRANSAKSI   │
 │                          │                        │                        ├───────────────────►│
 │                          │                        │                        │                    │
 │                          │                        │                        │  Cek konflik jadwal│
 │                          │                        │                        ├───────────────────►│ bookings
 │                          │                        │                        │◄───────────────────│
 │                          │                        │                        │                    │
 │                          │                        │  [jika ada konflik]    │                    │
 │                          │                        │  409 Conflict          │                    │
 │                          │                        │◄───────────────────────│                    │
 │                          │◄───────────────────────│                        │                    │
 │◄─────────────────────────│                        │                        │                    │
 │                          │                        │                        │                    │
 │                          │                        │  [jika OK]             │                    │
 │                          │                        │                        │  INSERT booking    │
 │                          │                        │                        ├───────────────────►│ bookings
 │                          │                        │                        │                    │
 │                          │                        │                        │  createForBooking()│
 │                          │                        │                        │  (PaymentService)  │
 │                          │                        │                        │                    │
 │                          │                        │                        │  INSERT payment    │
 │                          │                        │                        ├───────────────────►│ payments
 │                          │                        │                        │                    │
 │                          │                        │                        │  INSERT log        │
 │                          │                        │                        ├───────────────────►│ payment_status_logs
 │                          │                        │                        │                    │
 │                          │                        │                        │  COMMIT            │
 │                          │                        │                        ├───────────────────►│
 │                          │                        │◄───────────────────────│                    │
 │                          │◄───────────────────────│  201 Created           │                    │
 │◄─────────────────────────│                        │                        │                    │
```

### Steps

1. **User browses rooms** — public, no auth required
2. **User submits booking** (room_id, check_in, check_out, notes) — must be authenticated
3. **Validation** — `StoreBookingRequest` checks:
   - `room_id` exists in rooms table
   - `check_in >= today`
   - `check_out > check_in`
   - `notes` max 500 chars (optional)
4. **Service layer** — `BookingService::createBooking()`:
   - Checks room is active (`is_active = true`)
   - Checks no conflicting bookings exist (overlap detection in `getConflictingBookings()`)
   - Calculates `total_price = nights × room.price_per_night`
   - Creates booking with status `pending`
   - Creates payment via `PaymentService::createForBooking()` (status `pending`, 60 min expiry)
   - All wrapped in a database transaction
5. **Response** — 201 Created with booking + room data, or 409 Conflict / 422 Unprocessable

---

## 2. Payment Flow

```
User                     Browser/API              Controller           PaymentService           Database
 │                          │                        │                      │                     │
 │  Pilih metode bayar      │                        │                      │                     │
 ├─────────────────────────►│                        │                      │                     │
 │                          │  POST /payments/{id}/method?method=bank_transfer
 │                          ├───────────────────────►│                      │                     │
 │                          │                        │  selectMethod()     │                     │
 │                          │                        ├─────────────────────►│                     │
 │                          │                        │                      │ UPDATE method       │
 │                          │                        │                      ├────────────────────►│ payments
 │                          │◄───────────────────────│                      │                     │
 │◄─────────────────────────│                        │                      │                     │
 │                          │                        │                      │                     │
 │  Konfirmasi pembayaran   │                        │                      │                     │
 ├─────────────────────────►│                        │                      │                     │
 │                          │  POST /payments/{id}/process                 │                     │
 │                          │  {outcome: "success"}  │                      │                     │
 │                          ├───────────────────────►│                      │                     │
 │                          │                        │  processOutcome()   │                     │
 │                          │                        ├─────────────────────►│                     │
 │                          │                        │                      │                     │
 │                          │                        │                      │ BEGIN TRANSACTION   │
 │                          │                        │                      ├────────────────────►│
 │                          │                        │                      │                     │
 │                          │                        │  [jika expired]      │                     │
 │                          │                        │  expireIfOverdue()   │                     │
 │                          │                        │  throw Expired       │                     │
 │                          │                        │◄─────────────────────│                     │
 │                          │                        │                      │                     │
 │                          │                        │  [jika success]      │                     │
 │                          │                        │                      │ SELECT FOR UPDATE   │
 │                          │                        │                      ├────────────────────►│ payments
 │                          │                        │                      │ UPDATE status=paid  │
 │                          │                        │                      ├────────────────────►│ payments
 │                          │                        │                      │ INSERT log          │
 │                          │                        │                      ├────────────────────►│ payment_status_logs
 │                          │                        │                      │ UPDATE booking      │
 │                          │                        │                      │ SET status=confirmed│
 │                          │                        │                      ├────────────────────►│ bookings
 │                          │                        │                      │ COMMIT              │
 │                          │                        │                      ├────────────────────►│
 │                          │                        │                      │                     │
 │                          │                        │                      │ sendPaymentSucceeded│
 │                          │                        │                      │ (catch & log error) │
 │                          │                        │                      ├────────────────────►│ notifications
 │                          │◄───────────────────────│                      │                     │
 │◄─────────────────────────│  200 OK                │                      │                     │
```

### States & Transitions

```
                  ┌──────────┐
                  │  PENDING │ (60 menit expiry)
                  └────┬─────┘
                       │
         ┌─────────────┼─────────────┬──────────────┐
         ▼             ▼             ▼              ▼
     ┌──────┐    ┌────────┐    ┌─────────┐    ┌─────────┐
     │ PAID │    │ FAILED │    │ EXPIRED │    │ RETRY   │
     └──┬───┘    └────────┘    └─────────┘    │(new pay)│
        │                                      └─────────┘
        ▼
   ┌──────────┐
   │ REFUNDED │  (hanya jika booking dibatalkan)
   └──────────┘
```

### Payment Methods

| Method | Key | Notes |
|--------|-----|-------|
| Bank Transfer | `bank_transfer` | Manual verification by admin |
| E-Wallet | `e_wallet` | Automated simulation |
| Credit Card | `credit_card` | Automated simulation |

### Expiry Mechanism

- Pending payments have a 60-minute expiry window (`PaymentService::EXPIRY_MINUTES`)
- `ExpirePendingPayments` Artisan command runs every 5 minutes via scheduler (`routes/console.php:11`)
- Command queries all `pending` payments where `expires_at < now()`, processes in chunks of 100
- Lazy expiry also happens when user views a payment (`PaymentController::show()` calls `expireIfOverdue()`)
- When a payment expires, the associated booking is cancelled (if still `pending`)

### Retry Mechanism

- Max **5 payment attempts** per booking (`PaymentService::MAX_ATTEMPTS`)
- Only failed payments can be retried
- The associated booking must still be in `pending` status
- Retry creates a **new** payment record (with fresh 60 min expiry)
- Active payment check uses row-level locking (`SELECT ... FOR UPDATE`) to prevent race conditions

### Concurrency Safety

- All status transitions run inside `DB::transaction()`
- Row-level lock (`lockForUpdate()`) on the payment record ensures serial access
- `createForBooking()` also locks existing payments to prevent duplicate active payments
- Partial unique index on `payments(booking_id) WHERE status IN ('pending','paid')` (SQLite/Postgres)

---

## 3. Notification Flow

```
┌──────────┐     ┌───────────────────┐     ┌──────────────────┐     ┌──────────────┐
│  Event   │────►│  NotificationService │────►  Create record  │────►  notifications │
└──────────┘     └───────────────────┘     └──────────────────┘     └──────────────┘
                                                      │
                                                      ▼
                                               ┌──────────────┐
                                               │  Log on fail  │
                                               └──────────────┘
```

**Notification failures are caught and logged, never re-thrown** (critical rule).

### Notification Types

| Type | Trigger | Title | Message contains |
|------|---------|-------|------------------|
| `booking_confirmed` | Booking confirmed (via payment success or admin) | Booking Dikonfirmasi | Room name, check-in, check-out |
| `booking_cancelled` | User cancels booking | Booking Dibatalkan | Room name |
| `status_updated` | Admin updates booking status | Status Booking Diperbarui | Old status → new status |
| `payment_succeeded` | Payment success | Pembayaran Berhasil | Booking ID, payment reference |
| `payment_failed` | Payment fails | Pembayaran Gagal | Booking ID, payment status |
| `payment_expired` | Payment expires | Pembayaran Kedaluwarsa | Booking ID, payment status |
| `payment_refunded` | Payment refunded | Pembayaran Direfund | Booking ID, refund timestamp |

---

## 4. Admin Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                        Admin Panel                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌─────────────────────┐    ┌──────────────────────────┐        │
│  │  Room Management    │    │  Booking Management       │        │
│  │  ────────────────   │    │  ──────────────────────   │        │
│  │  • Create room      │    │  • View all bookings      │        │
│  │  • Edit room        │    │  • Filter by status/room  │        │
│  │  • Soft-delete room │    │  • Update status           │        │
│  │  • View conflicts   │    │  • View conflicts          │        │
│  └─────────────────────┘    └──────────────────────────┘        │
│                                                                   │
│  ┌──────────────────────┐                                       │
│  │  Payment Management  │                                       │
│  │  ──────────────────  │                                       │
│  │  • View all payments │                                       │
│  │  • Filter by status  │                                       │
│  │  • Override to paid  │─► Confirms booking + sets paid_at     │
│  │  • Override to failed│─► Sets failure_reason + verified_by   │
│  │  • Override to refund│─► Requires booking cancelled           │
│  └──────────────────────┘                                       │
└─────────────────────────────────────────────────────────────────┘
```

### Admin Access Control

- API routes: `middleware('auth:sanctum')` + `middleware('admin')` + prefix `admin/`
- Web routes: `middleware(['auth', 'admin'])` + prefix `admin/`
- `AdminMiddleware` checks `$user->role === 'admin'`, returns 403 JSON for API, 403 abort for web
- Policies also enforce admin-only for room create/update/delete

---

## 5. Scheduler

```
┌──────────────┐    every 5 min    ┌─────────────────────────┐
│  Console     │──────────────────►│  payments:expire        │
│  Kernel      │                   │  ─────────────────────  │
└──────────────┘                   │  SELECT pending WHERE   │
                                   │  expires_at < now()     │
                                   │  → expireIfOverdue()    │
                                   │  → cancel booking       │
                                   └─────────────────────────┘
```

Defined in `routes/console.php:11`:
```php
Schedule::command('payments:expire')->everyFiveMinutes()->withoutOverlapping();
```
