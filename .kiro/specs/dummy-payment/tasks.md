# Implementation Plan: Dummy Payment

## Overview

This plan implements a simulated payment workflow for the Bestay booking system. A `Payment` entity is created alongside every `Booking`, progressing through a state machine (`pending → paid|failed|expired → refunded`). The implementation covers database schema, models, service layer with state-machine logic, API and Web controllers, authorization policies, scheduled expiry, notifications, and property-based tests using Eris.

## Tasks

- [x] 1. Database migrations and model foundations
  - [x] 1.1 Create `payments` table migration
    - Create migration file `database/migrations/xxxx_xx_xx_create_payments_table.php`
    - Define columns: `id`, `booking_id` (FK), `reference` (unique, 64 chars), `amount` (decimal 10,2), `method` (nullable, 32 chars), `status` (default `pending`, 16 chars), `failure_reason` (nullable text), `paid_at`, `expires_at`, `refunded_at`, `verified_by` (FK nullable to users), `verified_at`, `timestamps`
    - Add indexes: `['booking_id', 'status']`, `status`, `expires_at`
    - Add partial unique index for SQLite: `CREATE UNIQUE INDEX payments_one_active_per_booking ON payments(booking_id) WHERE status IN ('pending', 'paid')`
    - _Requirements: 1.1, 1.3, 1.4, 1.5, 9.1, 9.2, 9.3, 9.4, 9.6_

  - [x] 1.2 Create `payment_status_logs` table migration
    - Create migration file `database/migrations/xxxx_xx_xx_create_payment_status_logs_table.php`
    - Define columns: `id`, `payment_id` (FK), `from_status` (nullable, 16 chars), `to_status` (16 chars), `actor_user_id` (FK nullable to users), `actor_type` (16 chars: guest|admin|system), `reason` (nullable, 500 chars), `created_at` (useCurrent)
    - Add index: `['payment_id', 'created_at']`
    - _Requirements: 7.6, 8.6_

  - [x] 1.3 Create `alter_bookings_default_status` migration
    - Change the default value of `bookings.status` column from `confirmed` to `pending` using `Schema::table` with `->change()`
    - _Requirements: 1.6_

  - [x] 1.4 Create `Payment` Eloquent model
    - Create `app/Models/Payment.php` with constants for statuses (`STATUS_PENDING`, `STATUS_PAID`, `STATUS_FAILED`, `STATUS_EXPIRED`, `STATUS_REFUNDED`), methods (`METHOD_BANK_TRANSFER`, `METHOD_E_WALLET`, `METHOD_CREDIT_CARD`), and arrays (`METHODS`, `STATUSES`, `TERMINAL_STATUSES`)
    - Define `$fillable`, `$casts` (amount as decimal:2, paid_at/expires_at/refunded_at/verified_at as datetime)
    - Define relationships: `booking()` (BelongsTo), `statusLogs()` (HasMany), `verifier()` (BelongsTo User)
    - Add helper methods: `isExpired()`, `isTerminal()`, `isActive()`
    - _Requirements: 8.1, 8.3, 9.2_

  - [x] 1.5 Create `PaymentStatusLog` Eloquent model
    - Create `app/Models/PaymentStatusLog.php` with `$fillable` and relationships: `payment()` (BelongsTo), `actor()` (BelongsTo User)
    - _Requirements: 7.6, 8.6_

  - [x] 1.6 Add payment relationships to `Booking` model
    - Add `payments(): HasMany` relationship
    - Add `activePayment(): HasOne` relationship (whereIn status pending/paid, latestOfMany)
    - _Requirements: 1.1, 9.1_

- [x] 2. Payment service exceptions and core service
  - [x] 2.1 Create custom exception classes
    - Create `app/Services/Payments/Exceptions/InvalidPaymentTransitionException.php` (extends `\DomainException`, carries `from` and `to`)
    - Create `app/Services/Payments/Exceptions/PaymentTerminalStatusException.php` (extends `InvalidPaymentTransitionException`)
    - Create `app/Services/Payments/Exceptions/ActivePaymentExistsException.php` (extends `\RuntimeException`)
    - Create `app/Services/Payments/Exceptions/PaymentExpiredException.php` (extends `\DomainException`)
    - Create `app/Services/Payments/Exceptions/InvalidPaymentAmountException.php` (extends `\InvalidArgumentException`)
    - _Requirements: 8.2, 8.3, 9.4, 9.5_

  - [x] 2.2 Implement `PaymentService` with state machine logic
    - Create `app/Services/PaymentService.php`
    - Define constants: `EXPIRY_MINUTES = 60`, `MAX_ATTEMPTS = 5`, `ALLOWED_TRANSITIONS` array
    - Implement `canTransition(string $from, string $to): bool` — pure static method checking the whitelist
    - Implement `generateReference(Carbon $now = null): string` — generates `PAY-YYYYMMDD-XXXXXX` format
    - Implement private `transition()` method wrapping `DB::transaction` + `lockForUpdate` + audit log creation
    - Implement `createForBooking(Booking $booking): Payment` — checks no active payment exists, creates payment with amount = booking total_price, expires_at = now + 60 min, logs creation
    - Implement `selectMethod(Payment $payment, string $method): Payment` — validates pending status and valid method
    - Implement `processOutcome(Payment $payment, string $outcome, ?string $failureReason = null): Payment` — handles success (→ paid, confirms booking) and fail (→ failed, stores reason)
    - Implement `expireIfOverdue(Payment $payment, string $actorType = 'system', ?User $actor = null): Payment` — checks expires_at < now, transitions to expired, cancels booking if pending
    - Implement `refundOnBookingCancellation(Booking $booking, ?User $actor = null): ?Payment` — idempotent: paid → refunded, pending → expired, terminal → no-op
    - Implement `adminOverride(Payment $payment, string $targetStatus, User $admin, ?string $reason = null): Payment` — validates admin preconditions for paid/failed/refunded targets
    - Constructor injection of `NotificationService`; call notification methods after commit
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.7, 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 4.1, 4.2, 4.3, 6.1, 6.2, 6.3, 6.5, 7.1, 7.4, 7.5, 7.6, 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 9.1, 9.4_

  - [ ]* 2.3 Write property test: Transition whitelist is authoritative (Property 12)
    - **Property 12: Transition whitelist is authoritative**
    - **Validates: Requirements 8.1, 8.2, 8.3**

  - [ ]* 2.4 Write property test: Payment reference uniqueness and length bounds (Property 3)
    - **Property 3: Payment reference uniqueness and length bounds**
    - **Validates: Requirements 1.4, 9.3, 9.6**

  - [ ]* 2.5 Write property test: Amount validation (Property 15)
    - **Property 15: Amount validation**
    - **Validates: Requirements 9.2, 9.5**

- [x] 3. Checkpoint - Ensure migrations and core service work
  - Ensure all tests pass, ask the user if questions arise.

- [x] 4. Request validation and policy
  - [x] 4.1 Create `SelectPaymentMethodRequest` form request
    - Create `app/Http/Requests/SelectPaymentMethodRequest.php`
    - Validate: `method` required, string, in `Payment::METHODS`
    - _Requirements: 2.2, 2.3_

  - [x] 4.2 Create `ProcessPaymentRequest` form request
    - Create `app/Http/Requests/ProcessPaymentRequest.php`
    - Validate: `outcome` required, string, in `['success', 'fail']`; `failure_reason` required_if outcome is fail, nullable, string, min:1, max:500
    - _Requirements: 3.1, 3.3, 3.7, 3.8_

  - [x] 4.3 Create `AdminUpdatePaymentStatusRequest` form request
    - Create `app/Http/Requests/AdminUpdatePaymentStatusRequest.php`
    - Validate: `status` required, string, in `[paid, failed, refunded]`; `reason` nullable, string, max:500
    - _Requirements: 7.1, 7.3_

  - [x] 4.4 Create `AdminPaymentIndexRequest` form request
    - Create `app/Http/Requests/AdminPaymentIndexRequest.php`
    - Validate: `status` nullable, in `Payment::STATUSES`; `method` nullable, in `Payment::METHODS`; `booking_id` nullable, integer, exists:bookings,id; `page` nullable, integer, min:1
    - _Requirements: 5.7, 5.8_

  - [x] 4.5 Create `PaymentPolicy`
    - Create `app/Policies/PaymentPolicy.php`
    - Implement `viewAny`, `view` (owner or admin), `process` (owner + pending), `selectMethod` (owner + pending), `adminOverride` (admin only)
    - _Requirements: 5.3, 5.5, 5.6, 7.2_

- [x] 5. API controllers
  - [x] 5.1 Implement `PaymentController` (API)
    - Create `app/Http/Controllers/PaymentController.php`
    - Implement `index(Request)` — list user's payments, paginate 20, ordered by created_at desc
    - Implement `show(Payment)` — authorize via policy, lazy-expire if overdue, return payment details
    - Implement `selectMethod(SelectPaymentMethodRequest, Payment)` — authorize, delegate to PaymentService
    - Implement `process(ProcessPaymentRequest, Payment)` — authorize, delegate to PaymentService::processOutcome
    - Implement `retry(Payment)` — authorize, validate retry conditions, delegate to PaymentService::createForBooking
    - Use try/catch pattern for custom exceptions → HTTP responses (409, 410, 422)
    - _Requirements: 2.2, 3.1, 3.4, 3.5, 4.1, 4.3, 5.1, 5.2, 5.3, 5.4, 5.5_

  - [x] 5.2 Implement `AdminPaymentController` (API)
    - Create `app/Http/Controllers/AdminPaymentController.php`
    - Implement `index(AdminPaymentIndexRequest)` — list all payments with optional filters (status, method, booking_id), paginate 20
    - Implement `show(Payment)` — admin bypass via policy
    - Implement `updateStatus(AdminUpdatePaymentStatusRequest, Payment)` — delegate to PaymentService::adminOverride
    - _Requirements: 5.7, 5.8, 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_

  - [x] 5.3 Register API routes
    - Add payment routes in `routes/api.php` under auth:sanctum middleware
    - Guest routes: `GET /payments`, `GET /payments/{payment}`, `POST /payments/{payment}/method`, `POST /payments/{payment}/process`, `POST /payments/{payment}/retry`
    - Admin routes: `GET /admin/payments`, `GET /admin/payments/{payment}`, `POST /admin/payments/{payment}/status` under admin middleware
    - _Requirements: 5.1, 5.5, 5.6, 7.2_

- [x] 6. Integrate PaymentService with BookingService
  - [x] 6.1 Modify `BookingService::createBooking` to create Payment
    - Inject `PaymentService` into `BookingService` constructor
    - Change `'status' => 'confirmed'` to `'status' => 'pending'` in the booking creation
    - After `Booking::create(...)` inside the transaction, call `$this->paymentService->createForBooking($booking)`
    - Remove the `sendBookingConfirmation` call (booking is now pending, confirmation happens on payment success)
    - _Requirements: 1.1, 1.6, 1.7_

  - [x] 6.2 Modify `BookingService::cancelBooking` to trigger payment refund
    - After updating booking status to `cancelled`, call `$this->paymentService->refundOnBookingCancellation($booking)` within the same transaction scope
    - _Requirements: 6.1, 6.2, 6.5_

  - [ ]* 6.3 Write property test: Booking creation produces exactly one matching pending Payment (Property 1)
    - **Property 1: Booking creation produces exactly one matching pending Payment**
    - **Validates: Requirements 1.1, 1.2, 1.3, 1.5, 1.6, 4.5**

  - [ ]* 6.4 Write property test: Booking + Payment creation is transactionally atomic (Property 2)
    - **Property 2: Booking + Payment creation is transactionally atomic**
    - **Validates: Requirements 1.7**

- [x] 7. Checkpoint - Ensure booking integration and API controllers work
  - Ensure all tests pass, ask the user if questions arise.

- [x] 8. Web controllers and Blade views
  - [x] 8.1 Implement `Web\PaymentController`
    - Create `app/Http/Controllers/Web/PaymentController.php`
    - Implement `show(Booking)` — show current active payment for a booking, lazy-expire if overdue
    - Implement `selectMethod(SelectPaymentMethodRequest, Payment)` — select method, redirect back with success
    - Implement `confirmForm(Payment)` — render the success/fail confirmation form
    - Implement `confirm(ProcessPaymentRequest, Payment)` — process outcome, redirect with result
    - Implement `retry(Payment)` — create new attempt, redirect to payment view
    - _Requirements: 2.1, 2.2, 3.1, 3.4, 4.1_

  - [x] 8.2 Create Blade views for payment flow
    - Create `resources/views/payments/show.blade.php` — displays payment details, method selection (3 radio buttons: bank_transfer, e_wallet, credit_card), expiry countdown
    - Create `resources/views/payments/confirm.blade.php` — displays success/fail buttons with failure reason textarea
    - Follow existing Blade patterns from `resources/views/bookings/`
    - _Requirements: 2.1, 3.1, 3.3_

  - [x] 8.3 Register Web routes
    - Add payment web routes in `routes/web.php` under auth middleware
    - `GET /bookings/{booking}/payment`, `POST /payments/{payment}/method`, `GET /payments/{payment}/confirm`, `POST /payments/{payment}/confirm`, `POST /payments/{payment}/retry`
    - _Requirements: 2.1, 3.1, 3.4_

- [x] 9. NotificationService extensions and expiry command
  - [x] 9.1 Extend `NotificationService` with payment notification methods
    - Add `sendPaymentSucceeded(Payment $payment): void` — creates notification with booking id and payment reference
    - Add `sendPaymentFailed(Payment $payment): void` — creates notification with booking id and new status
    - Add `sendPaymentExpired(Payment $payment): void` — creates notification with booking id and expired status
    - Add `sendPaymentRefunded(Payment $payment): void` — creates notification with booking id and refunded_at timestamp
    - Wrap each in try/catch that logs failures without re-throwing (Requirement 10.4)
    - _Requirements: 10.1, 10.2, 10.3, 10.4_

  - [x] 9.2 Create `ExpirePendingPayments` Artisan command
    - Create `app/Console/Commands/ExpirePendingPayments.php`
    - Query `Payment::where('status', 'pending')->where('expires_at', '<', now())`
    - Use `chunkById(100, ...)` to iterate and call `PaymentService::expireIfOverdue` for each
    - _Requirements: 4.4_

  - [x] 9.3 Register scheduled command in `routes/console.php`
    - Add `Schedule::command('payments:expire')->everyFiveMinutes()->withoutOverlapping()`
    - _Requirements: 4.4_

  - [ ]* 9.4 Write property test: Expiry rules (Property 9)
    - **Property 9: Expiry rules**
    - **Validates: Requirements 4.1, 4.2, 4.3, 4.4**

  - [ ]* 9.5 Write property test: Every committed transition produces exactly one notification (Property 16)
    - **Property 16: Every committed transition produces exactly one notification for the owner**
    - **Validates: Requirements 4.6, 10.1, 10.2, 10.3, 10.4**

- [x] 10. Checkpoint - Ensure notifications, expiry, and web flow work
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 11. Property-based tests for payment lifecycle
  - [ ]* 11.1 Write property test: Method selection rules (Property 4)
    - **Property 4: Method selection is allowed iff value is valid and payment is pending**
    - **Validates: Requirements 2.2, 2.3, 2.4**

  - [ ]* 11.2 Write property test: Success outcome atomically pays Payment and confirms Booking (Property 5)
    - **Property 5: Success outcome atomically pays the Payment and confirms the Booking**
    - **Validates: Requirements 3.1, 3.2, 3.6**

  - [ ]* 11.3 Write property test: Fail outcome requires reason of length 1..500 (Property 6)
    - **Property 6: Fail outcome requires a reason of length 1..500**
    - **Validates: Requirements 3.3, 3.8**

  - [ ]* 11.4 Write property test: Outcome submissions against non-pending Payment are no-ops (Property 7)
    - **Property 7: Outcome submissions against a non-pending Payment are no-ops**
    - **Validates: Requirements 3.5, 3.7**

  - [ ]* 11.5 Write property test: Retry policy (Property 8)
    - **Property 8: Retry policy**
    - **Validates: Requirements 3.4**

  - [ ]* 11.6 Write property test: Booking cancellation drives Payment state with idempotence (Property 10)
    - **Property 10: Booking cancellation drives Payment state with idempotence**
    - **Validates: Requirements 6.1, 6.2, 6.3, 6.5**

  - [ ]* 11.7 Write property test: Admin override authorization and preconditions (Property 11)
    - **Property 11: Admin override authorization and preconditions**
    - **Validates: Requirements 7.1, 7.2, 7.3, 7.4, 7.5**

  - [ ]* 11.8 Write property test: Every committed transition produces exactly one audit log row (Property 13)
    - **Property 13: Every committed transition produces exactly one audit log row**
    - **Validates: Requirements 7.6, 8.6**

  - [ ]* 11.9 Write property test: At most one active Payment per Booking (Property 14)
    - **Property 14: At most one active Payment per Booking**
    - **Validates: Requirements 9.1, 9.4**

  - [ ]* 11.10 Write property test: Listing ordering, ownership, and pagination (Property 17)
    - **Property 17: Listing ordering, ownership, and pagination**
    - **Validates: Requirements 5.1, 5.7**

  - [ ]* 11.11 Write property test: Access-control invariants (Property 18)
    - **Property 18: Access-control invariants**
    - **Validates: Requirements 5.3, 5.4, 5.5, 5.6, 5.8**

- [ ] 12. Feature and integration tests
  - [ ]* 12.1 Write feature tests for payment creation and booking integration
    - Test booking creation returns 201 with payment in response
    - Test payment amount matches booking total_price
    - Test booking status is `pending` after creation
    - Test transaction rollback when payment creation fails (Requirement 1.7, 1.8)
    - _Requirements: 1.1, 1.2, 1.6, 1.7, 1.8_

  - [ ]* 12.2 Write feature tests for payment method selection and processing
    - Test selecting valid method on pending payment succeeds
    - Test selecting invalid method returns 422
    - Test selecting method on non-pending payment returns error
    - Test process success → paid + booking confirmed
    - Test process fail with valid reason → failed
    - Test process fail with invalid reason → 422
    - Test process on non-pending payment → rejected
    - _Requirements: 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 3.5, 3.7, 3.8_

  - [ ]* 12.3 Write feature tests for expiry and retry
    - Test viewing expired payment triggers lazy expiry
    - Test processing expired payment returns 410
    - Test retry creates new payment when attempts < 5
    - Test retry rejected when attempts >= 5
    - Test scheduled command expires overdue payments
    - _Requirements: 3.4, 4.1, 4.2, 4.3, 4.4_

  - [ ]* 12.4 Write feature tests for refund on cancellation
    - Test cancelling booking with paid payment → refunded
    - Test cancelling booking with pending payment → expired
    - Test cancelling booking with terminal payment → no-op
    - Test cancelling booking with no payment → no error
    - Test idempotent cancellation
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_

  - [ ]* 12.5 Write feature tests for admin endpoints and policy
    - Test admin can list all payments with filters
    - Test admin can override payment status
    - Test admin refund requires paid status + cancelled booking
    - Test non-admin gets 403 on admin endpoints
    - Test unauthenticated gets 401
    - Test guest accessing other user's payment gets 403
    - _Requirements: 5.6, 5.7, 5.8, 7.1, 7.2, 7.3, 7.4, 7.5_

  - [ ]* 12.6 Write feature tests for notifications
    - Test notification created on payment success with reference
    - Test notification created on payment failure with status
    - Test notification created on payment expiry
    - Test notification created on refund with refunded_at
    - Test notification failure does not reverse payment transition
    - _Requirements: 10.1, 10.2, 10.3, 10.4_

- [x] 13. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties from the design document using giorgiosironi/eris
- Unit tests validate specific examples and edge cases
- The `PaymentService` is the single source of truth for state transitions — controllers never mutate payment status directly
- All state transitions are wrapped in `DB::transaction` with `lockForUpdate` for atomicity
- Notifications are dispatched after commit to avoid blocking payment transitions

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "1.2", "1.3"] },
    { "id": 1, "tasks": ["1.4", "1.5", "1.6"] },
    { "id": 2, "tasks": ["2.1"] },
    { "id": 3, "tasks": ["2.2"] },
    { "id": 4, "tasks": ["2.3", "2.4", "2.5", "4.1", "4.2", "4.3", "4.4", "4.5"] },
    { "id": 5, "tasks": ["5.1", "5.2", "9.1"] },
    { "id": 6, "tasks": ["5.3", "6.1", "6.2"] },
    { "id": 7, "tasks": ["6.3", "6.4", "9.2", "9.3"] },
    { "id": 8, "tasks": ["8.1", "8.2"] },
    { "id": 9, "tasks": ["8.3", "9.4", "9.5"] },
    { "id": 10, "tasks": ["11.1", "11.2", "11.3", "11.4", "11.5", "11.6", "11.7", "11.8", "11.9", "11.10", "11.11"] },
    { "id": 11, "tasks": ["12.1", "12.2", "12.3", "12.4", "12.5", "12.6"] }
  ]
}
```
