# ERD — Entity Relationship Diagram

## Overview

```
┌──────────┐       ┌──────────────┐       ┌──────────┐
│   users   │       │   bookings   │       │  rooms   │
├──────────┤       ├──────────────┤       ├──────────┤
│ id (PK)  │◄──────│ user_id (FK) │       │ id (PK) │
│ name     │       │ room_id (FK) │──────►│ name    │
│ email    │       │ check_in     │       │ type    │
│ password │       │ check_out    │       │ price   │
│ role     │       │ total_price  │       │ capacity│
└──────────┘       │ status       │       │ is_active│
                   │ notes        │       └──────────┘
                   └──────┬───────┘
                          │
                   ┌──────▼───────┐       ┌──────────────────────┐
                   │   payments   │       │ payment_status_logs  │
                   ├──────────────┤       ├──────────────────────┤
                   │ id (PK)     │◄──────│ payment_id (FK)      │
                   │ booking_id  │       │ from_status          │
                   │ reference   │       │ to_status            │
                   │ amount      │       │ actor_user_id (FK)   │
                   │ method      │       │ actor_type           │
                   │ status      │       │ reason               │
                   │ paid_at     │       │ created_at           │
                   │ expires_at  │       └──────────────────────┘
                   │ refunded_at │
                   │ verified_by │──► users.id
                   │ verified_at │
                   └──────────────┘

                   ┌────────────────┐
                   │ notifications  │
                   ├────────────────┤
                   │ id (PK)       │
                   │ user_id (FK)  │──► users.id
                   │ booking_id(FK)│──► bookings.id
                   │ type          │
                   │ title         │
                   │ message       │
                   │ is_read       │
                   │ read_at       │
                   └────────────────┘
```

## Tables

### users

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| name | VARCHAR(255) | NOT NULL | |
| email | VARCHAR(255) | NOT NULL, UNIQUE | |
| email_verified_at | TIMESTAMP | NULLABLE | |
| password | VARCHAR(255) | NOT NULL | Hashed via bcrypt |
| role | ENUM('user','admin') | NOT NULL, DEFAULT 'user' | |
| remember_token | VARCHAR(100) | NULLABLE | Laravel auth |
| created_at | TIMESTAMP | NULLABLE | |
| updated_at | TIMESTAMP | NULLABLE | |

**Relationships:**
- Has many `bookings` → `bookings.user_id`
- Has many `notifications` → `notifications.user_id`
- Has many payments as verifier → `payments.verified_by`

---

### rooms

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| name | VARCHAR(255) | NOT NULL | |
| type | VARCHAR(255) | NOT NULL | `standard`, `deluxe`, `suite`, `family` |
| description | TEXT | NULLABLE | |
| price_per_night | DECIMAL(10,2) | NOT NULL | In IDR |
| capacity | INTEGER | NOT NULL | Max guests |
| image_url | VARCHAR(255) | NULLABLE | Unsplash URL |
| is_active | BOOLEAN | NOT NULL, DEFAULT TRUE | Soft-delete flag |
| created_at | TIMESTAMP | NULLABLE | |
| updated_at | TIMESTAMP | NULLABLE | |

**Index:** `(type, is_active, price_per_night, capacity)`

**Relationships:**
- Has many `bookings` → `bookings.room_id`

---

### bookings

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| user_id | BIGINT UNSIGNED | FK → users.id, NOT NULL, CASCADE DELETE | |
| room_id | BIGINT UNSIGNED | FK → rooms.id, NOT NULL, CASCADE DELETE | |
| check_in | DATE | NOT NULL | |
| check_out | DATE | NOT NULL | |
| total_price | DECIMAL(10,2) | NOT NULL | nights × price_per_night |
| status | VARCHAR(255) | NOT NULL, DEFAULT 'pending' | `pending`, `confirmed`, `cancelled`, `completed` |
| notes | TEXT | NULLABLE | |
| created_at | TIMESTAMP | NULLABLE | |
| updated_at | TIMESTAMP | NULLABLE | |

**Index:** `(room_id, check_in, check_out, status)`

**Status transitions:**
```
pending → confirmed | cancelled
confirmed → cancelled | completed
cancelled → (terminal)
completed → (terminal)
```

**Relationships:**
- Belongs to `user` → `users.id`
- Belongs to `room` → `rooms.id`
- Has many `payments` → `payments.booking_id`
- Has one `activePayment` → `payments` where status IN ('pending','paid'), latest
- Has many `notifications` → `notifications.booking_id`

---

### payments

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| booking_id | BIGINT UNSIGNED | FK → bookings.id, NOT NULL, CASCADE DELETE | |
| reference | VARCHAR(64) | NOT NULL, UNIQUE | Format: `PAY-YYYYMMDD-XXXXXX` |
| amount | DECIMAL(10,2) | NOT NULL | Matches booking total_price |
| method | VARCHAR(32) | NULLABLE | `bank_transfer`, `e_wallet`, `credit_card` |
| status | VARCHAR(16) | NOT NULL, DEFAULT 'pending' | `pending`, `paid`, `failed`, `expired`, `refunded` |
| failure_reason | TEXT | NULLABLE | Required when status=failed |
| paid_at | TIMESTAMP | NULLABLE | Set when status → paid |
| expires_at | TIMESTAMP | NOT NULL | = created_at + 60 minutes |
| refunded_at | TIMESTAMP | NULLABLE | Set when status → refunded |
| verified_by | BIGINT UNSIGNED | FK → users.id, NULLABLE, NULL ON DELETE | Admin who verified |
| verified_at | TIMESTAMP | NULLABLE | |
| created_at | TIMESTAMP | NULLABLE | |
| updated_at | TIMESTAMP | NULLABLE | |

**Indexes:**
- `(booking_id, status)` — for active payment lookups
- `(status)` — for expiry queries
- `(expires_at)` — for expiry queries
- Partial unique (SQLite/Postgres only): `payments_one_active_per_booking` WHERE status IN ('pending','paid')

**Status transitions:**
```
pending → paid | failed | expired
paid → refunded
failed → (terminal — user can retry by creating new payment)
expired → (terminal)
refunded → (terminal)
```

**Relationships:**
- Belongs to `booking` → `bookings.id`
- Has many `statusLogs` → `payment_status_logs.payment_id`
- Belongs to `verifier` → `users.id`

---

### payment_status_logs

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| payment_id | BIGINT UNSIGNED | FK → payments.id, NOT NULL, CASCADE DELETE | |
| from_status | VARCHAR(16) | NULLABLE | NULL on initial creation |
| to_status | VARCHAR(16) | NOT NULL | |
| actor_user_id | BIGINT UNSIGNED | FK → users.id, NULLABLE, NULL ON DELETE | |
| actor_type | VARCHAR(16) | NOT NULL | `guest`, `admin`, `system` |
| reason | VARCHAR(500) | NULLABLE | |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | |

**Index:** `(payment_id, created_at)`

**Notes:**
- Immutable audit log — `UPDATED_AT = null` in model (no `updated_at` column)
- Every payment status change inserts one row
- `from_status = NULL` on initial `pending` creation

**Relationships:**
- Belongs to `payment` → `payments.id`
- Belongs to `actor` → `users.id`

---

### notifications

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| user_id | BIGINT UNSIGNED | FK → users.id, NOT NULL, CASCADE DELETE | |
| booking_id | BIGINT UNSIGNED | FK → bookings.id, NOT NULL, CASCADE DELETE | |
| type | VARCHAR(255) | NOT NULL | `booking_confirmed`, `booking_cancelled`, `status_updated`, `payment_succeeded`, `payment_failed`, `payment_expired`, `payment_refunded` |
| title | VARCHAR(255) | NOT NULL | In Indonesian |
| message | TEXT | NOT NULL | In Indonesian |
| is_read | BOOLEAN | NOT NULL, DEFAULT FALSE | |
| read_at | TIMESTAMP | NULLABLE | |
| created_at | TIMESTAMP | NULLABLE | |
| updated_at | TIMESTAMP | NULLABLE | |

**Relationships:**
- Belongs to `user` → `users.id`
- Belongs to `booking` → `bookings.id`
