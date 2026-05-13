# Implementation Plan: Bestay Booking System

## Overview

This plan implements the Bestay hotel booking system using Laravel 12 with Laravel Sanctum authentication, role-based access control, booking conflict detection, and a notification system. Tasks are structured to build incrementally from data layer through business logic to API endpoints and testing.

## Tasks

- [x] 1. Set up project foundation and data layer
  - [x] 1.1 Install Laravel Sanctum and configure authentication
    - Run `composer require laravel/sanctum` and publish config
    - Add `HasApiTokens` trait to User model
    - Configure Sanctum middleware in `bootstrap/app.php`
    - Add `role` column (enum: user, admin) to users migration
    - Update User model with `$fillable`, `casts`, `isAdmin()` helper, and relationships
    - _Requirements: 2.1, 10.1_

  - [x] 1.2 Create Room model, migration, and factory
    - Create migration with columns: name, type (enum: standard/deluxe/suite), description, price_per_night (decimal 10,2), capacity (integer), image_url, is_active (boolean, default true)
    - Create Room model with `$fillable`, `casts`, `scopeActive`, `scopeAvailableBetween`, and `bookings()` relationship
    - Create RoomFactory for test data generation
    - Add composite index on `rooms(type, is_active, price_per_night, capacity)`
    - _Requirements: 3.1, 3.2, 3.3, 9.2, 9.3, 9.4_

  - [x] 1.3 Create Booking model, migration, and factory
    - Create migration with columns: user_id (FK), room_id (FK), check_in (date), check_out (date), total_price (decimal 10,2), status (enum: pending/confirmed/cancelled/completed), notes (text nullable)
    - Add composite index on `bookings(room_id, check_in, check_out, status)` for conflict detection performance
    - Create Booking model with `$fillable`, `casts`, relationships (`user`, `room`, `notifications`), and accessors (`duration`, `is_active`)
    - Create BookingFactory for test data generation
    - _Requirements: 5.1, 5.2, 9.1, 9.5, 9.6_

  - [x] 1.4 Create Notification model, migration, and factory
    - Create migration with columns: user_id (FK), booking_id (FK), type (string), title (string), message (text), is_read (boolean, default false), read_at (timestamp nullable)
    - Create Notification model with `$fillable`, `casts`, relationships (`user`, `booking`), and `scopeUnread`
    - Create NotificationFactory for test data generation
    - _Requirements: 8.1, 8.4_

  - [x] 1.5 Create database seeder with sample data
    - Create admin user and regular user accounts
    - Seed sample rooms of each type (standard, deluxe, suite)
    - Seed sample bookings and notifications for testing
    - _Requirements: 3.1, 5.1_

- [x] 2. Implement authentication system
  - [x] 2.1 Create Form Request classes for auth validation
    - Create `RegisterRequest` with rules: name (required, string, max:255), email (required, email, unique:users, max:255), password (required, min:8, max:128, confirmed)
    - Create `LoginRequest` with rules: email (required, email), password (required, string)
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 2.3_

  - [x] 2.2 Implement AuthController
    - Implement `register()`: create user with role 'user', hash password, issue Sanctum token, return 201 with user data and token
    - Implement `login()`: validate credentials, issue token, return user data and token; return 401 on failure
    - Implement `logout()`: revoke current token, return 200 confirmation
    - Implement `profile()`: return authenticated user data (id, name, email, email_verified_at)
    - _Requirements: 1.1, 2.1, 2.2, 2.3, 2.4, 2.5_

  - [x] 2.3 Configure API routes for authentication
    - Register public routes: POST `/api/register`, POST `/api/login`
    - Register protected routes (Sanctum middleware): POST `/api/logout`, GET `/api/profile`
    - Apply rate limiting (5 requests/minute) on login and register endpoints
    - _Requirements: 2.6, 10.1, 10.4, 10.5_

  - [ ]* 2.4 Write feature tests for authentication endpoints
    - Test successful registration returns 201 with user and token
    - Test duplicate email returns 422
    - Test invalid password returns 422
    - Test successful login returns token
    - Test invalid credentials returns 401
    - Test logout revokes token
    - Test unauthenticated access returns 401
    - **Property 8: Authentication Enforcement**
    - **Property 9: Registration Validation**
    - **Property 11: Token Lifecycle**
    - **Validates: Requirements 1.1, 1.2, 1.3, 1.4, 2.1, 2.2, 2.4, 2.6, 10.1**

- [x] 3. Checkpoint - Ensure migrations run and auth tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 4. Implement room management
  - [x] 4.1 Create Form Request classes for room validation
    - Create `StoreRoomRequest` with rules: name (required, string, max:255), type (required, in:standard,deluxe,suite), description (nullable, string), price_per_night (required, numeric, min:0, max:99999999.99), capacity (required, integer, min:1, max:100), image_url (nullable, url), is_active (boolean)
    - Create `UpdateRoomRequest` with same rules but all fields optional (sometimes)
    - _Requirements: 3.3, 3.4, 3.9, 9.2, 9.3, 9.4_

  - [x] 4.2 Create RoomPolicy for authorization
    - Allow any user to `viewAny` and `view`
    - Allow only admin to `create`, `update`, `delete`
    - Register policy in AuthServiceProvider
    - _Requirements: 3.6, 10.2_

  - [x] 4.3 Implement RoomController
    - Implement `index()`: return paginated active rooms (15/page) with optional filters (type, min_price, max_price, capacity)
    - Implement `show()`: return room details or 404
    - Implement `store()`: admin creates room, return 201
    - Implement `update()`: admin updates room, return updated room
    - Implement `destroy()`: soft-delete (set is_active=false) if no active bookings, else return 409
    - Implement `availability()`: check room availability for date range using BookingService
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.7, 3.8, 3.10, 4.1, 4.2, 4.3, 4.4, 4.5_

  - [x] 4.4 Configure API routes for rooms
    - Register public routes: GET `/api/rooms`, GET `/api/rooms/{room}`
    - Register protected routes: GET `/api/rooms/{room}/availability`
    - Register admin routes: POST `/api/rooms`, PUT `/api/rooms/{room}`, DELETE `/api/rooms/{room}`
    - _Requirements: 3.1, 3.6, 4.1_

  - [ ]* 4.5 Write feature tests for room management
    - Test room listing with pagination and filters
    - Test room creation by admin (201) and rejection for non-admin (403)
    - Test room update and delete (soft-delete)
    - Test delete rejection when active bookings exist (409)
    - Test availability check returns correct result
    - **Property 6: Role-Based Access Control**
    - **Property 12: Room Filter Correctness**
    - **Validates: Requirements 3.1, 3.3, 3.5, 3.6, 3.7, 3.10, 4.1, 4.2, 4.3**

- [x] 5. Implement booking service and conflict detection
  - [x] 5.1 Implement BookingService
    - Implement `checkAvailability()`: query active bookings with overlap logic (check_in < checkOut AND check_out > checkIn)
    - Implement `getConflictingBookings()`: return collection of conflicting active bookings
    - Implement `createBooking()`: validate room active, check conflicts within DB transaction, calculate total_price, create booking with status 'confirmed'
    - Implement `cancelBooking()`: update status to 'cancelled'
    - Implement `updateStatus()`: validate transition rules (pending→confirmed/cancelled, confirmed→cancelled/completed), update status
    - Implement `getAllBookings()`: return paginated bookings with optional filters
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.8, 7.3, 7.4, 7.5, 9.6_

  - [ ]* 5.2 Write unit tests for BookingService conflict detection
    - Test overlapping bookings are detected correctly
    - Test non-overlapping bookings (adjacent dates) are allowed
    - Test only active bookings (pending/confirmed) are considered
    - Test total price calculation accuracy
    - Test status transition validation
    - **Property 1: No Double Booking**
    - **Validates: Requirements 5.3, 5.4, 9.6**

  - [ ]* 5.3 Write property test for total price consistency
    - **Property 2: Total Price Consistency**
    - For random valid date ranges and room prices, verify total_price = nights × price_per_night
    - **Validates: Requirements 5.2, 9.5**

  - [ ]* 5.4 Write property test for date range validity
    - **Property 3: Date Range Validity**
    - For any booking in the system, check_in is strictly before check_out
    - **Validates: Requirements 5.7, 9.1**

  - [ ]* 5.5 Write property test for conflict detection correctness
    - **Property 4: Conflict Detection Correctness**
    - For random room and date range, availability returns true iff no active bookings overlap
    - **Validates: Requirements 4.1, 4.2, 4.3**

- [x] 6. Implement booking endpoints for users
  - [x] 6.1 Create Form Request classes for booking validation
    - Create `StoreBookingRequest` with rules: room_id (required, exists:rooms,id), check_in (required, date, after_or_equal:today), check_out (required, date, after:check_in), notes (nullable, string, max:500)
    - _Requirements: 5.6, 5.7, 5.9, 5.10, 9.1_

  - [x] 6.2 Create BookingPolicy for authorization
    - Allow user to `viewAny` (own bookings only enforced in controller)
    - Allow user to `view` only own bookings
    - Allow user to `create` bookings
    - Allow user to `cancel` only own bookings with active status
    - Register policy
    - _Requirements: 6.3, 6.4, 6.5, 10.2_

  - [x] 6.3 Implement BookingController
    - Implement `index()`: return paginated bookings for authenticated user, sorted by created_at desc
    - Implement `store()`: use BookingService to create booking, return 201 with booking details; handle conflict (409) and room not active (422)
    - Implement `show()`: return booking with room details, enforce ownership via policy (403 if not owner)
    - Implement `cancel()`: cancel booking via BookingService, return updated booking; reject if status is cancelled/completed (422)
    - _Requirements: 5.1, 5.3, 5.5, 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_

  - [x] 6.4 Configure API routes for user bookings
    - Register protected routes: GET `/api/bookings`, POST `/api/bookings`, GET `/api/bookings/{booking}`, PATCH `/api/bookings/{booking}/cancel`
    - _Requirements: 6.1, 6.2_

  - [ ]* 6.5 Write feature tests for user booking endpoints
    - Test booking creation success (201) with correct total_price
    - Test booking conflict returns 409 with conflicting booking details
    - Test booking on inactive room returns 422
    - Test user can only see own bookings
    - Test user cannot view another user's booking (403)
    - Test cancellation of active booking
    - Test cancellation of already cancelled/completed booking (422)
    - **Property 5: User Authorization Isolation**
    - **Validates: Requirements 5.1, 5.3, 5.5, 6.1, 6.3, 6.4, 6.5**

- [x] 7. Checkpoint - Ensure booking creation and conflict detection work
  - Ensure all tests pass, ask the user if questions arise.

- [x] 8. Implement admin booking management
  - [x] 8.1 Create Form Request for status update
    - Create `UpdateStatusRequest` with rules: status (required, in:confirmed,cancelled,completed)
    - _Requirements: 7.3, 7.5_

  - [x] 8.2 Implement AdminBookingController
    - Implement `index()`: return paginated list of all bookings (15/page)
    - Implement `show()`: return complete booking details including user and room info
    - Implement `updateStatus()`: validate transition via BookingService, update status, trigger notification
    - Implement `conflicts()`: query and return all active bookings with overlapping dates on same room
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.8_

  - [x] 8.3 Create admin middleware and configure routes
    - Create or use existing middleware to check admin role
    - Register admin routes: GET `/api/admin/bookings`, GET `/api/admin/bookings/{booking}`, PATCH `/api/admin/bookings/{booking}/status`, GET `/api/admin/bookings/conflicts`
    - _Requirements: 7.7_

  - [ ]* 8.4 Write feature tests for admin booking management
    - Test admin can view all bookings
    - Test admin can update booking status with valid transitions
    - Test invalid status transitions return 422
    - Test non-admin access returns 403
    - Test conflicts endpoint returns overlapping bookings
    - **Property 7: Status Transition Validity**
    - **Validates: Requirements 7.1, 7.3, 7.4, 7.5, 7.7**

- [x] 9. Implement notification system
  - [x] 9.1 Implement NotificationService
    - Implement `sendBookingConfirmation()`: create notification with type 'booking_confirmed', title, and message for booking owner
    - Implement `sendBookingCancellation()`: create notification with type 'booking_cancelled'
    - Implement `sendStatusUpdate()`: create notification with type 'status_updated' indicating new status
    - _Requirements: 8.1, 8.2, 8.3_

  - [x] 9.2 Implement NotificationController
    - Implement `index()`: return paginated notifications for authenticated user, ordered by created_at desc
    - Implement `markAsRead()`: set is_read=true and read_at=now for single notification; enforce ownership (403)
    - Implement `markAllAsRead()`: update all unread notifications for user, return count of updated
    - Handle 404 for non-existent notifications
    - _Requirements: 8.4, 8.5, 8.6, 8.7, 8.8, 8.9_

  - [x] 9.3 Wire NotificationService into BookingService and controllers
    - Call `sendBookingConfirmation()` after successful booking creation in BookingService
    - Call `sendBookingCancellation()` after user cancels booking in BookingController
    - Call `sendStatusUpdate()` after admin changes status in AdminBookingController
    - _Requirements: 8.1, 8.2, 8.3_

  - [x] 9.4 Configure API routes for notifications
    - Register protected routes: GET `/api/notifications`, PATCH `/api/notifications/{notification}/read`, POST `/api/notifications/read-all`
    - _Requirements: 8.4, 8.5, 8.8_

  - [ ]* 9.5 Write feature tests for notification system
    - Test notification created on booking creation
    - Test notification created on booking cancellation
    - Test notification created on admin status change
    - Test user can only see own notifications
    - Test mark as read updates is_read and read_at
    - Test mark all as read returns correct count
    - Test accessing another user's notification returns 403
    - **Property 10: Notification on Booking Events**
    - **Validates: Requirements 8.1, 8.2, 8.3, 8.4, 8.5, 8.7, 8.8**

- [x] 10. Implement API security and rate limiting
  - [x] 10.1 Configure rate limiting on auth endpoints
    - Define rate limiter in `AppServiceProvider` or `RouteServiceProvider`: 5 requests/minute for login and register
    - Apply rate limiter to auth routes
    - Ensure 429 response includes Retry-After header
    - _Requirements: 10.4, 10.5_

  - [x] 10.2 Verify all FormRequest classes return proper 422 responses
    - Ensure all validation errors return structured JSON with field-specific messages
    - Verify unauthenticated JSON responses return 401 (configure exception handler for API)
    - _Requirements: 9.7, 10.3_

  - [ ]* 10.3 Write feature tests for security enforcement
    - Test rate limiting returns 429 after 5 requests
    - Test all protected endpoints return 401 without token
    - Test all admin endpoints return 403 for regular users
    - Test validation errors return proper 422 format
    - **Property 8: Authentication Enforcement**
    - **Property 6: Role-Based Access Control**
    - **Validates: Requirements 10.1, 10.2, 10.3, 10.4, 10.5, 10.6**

- [x] 11. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties from the design document
- Unit tests validate specific examples and edge cases
- The project uses PHPUnit (already configured in `phpunit.xml`) for all testing
- Laravel Sanctum must be installed as it's not yet in `composer.json`
- Database uses SQLite for development (already configured)

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "1.4"] },
    { "id": 1, "tasks": ["1.2", "1.3"] },
    { "id": 2, "tasks": ["1.5", "2.1", "4.1"] },
    { "id": 3, "tasks": ["2.2", "4.2"] },
    { "id": 4, "tasks": ["2.3", "4.3"] },
    { "id": 5, "tasks": ["2.4", "4.4"] },
    { "id": 6, "tasks": ["4.5", "5.1"] },
    { "id": 7, "tasks": ["5.2", "5.3", "5.4", "5.5", "6.1"] },
    { "id": 8, "tasks": ["6.2", "8.1"] },
    { "id": 9, "tasks": ["6.3", "8.2"] },
    { "id": 10, "tasks": ["6.4", "8.3"] },
    { "id": 11, "tasks": ["6.5", "8.4", "9.1"] },
    { "id": 12, "tasks": ["9.2", "9.3"] },
    { "id": 13, "tasks": ["9.4"] },
    { "id": 14, "tasks": ["9.5", "10.1"] },
    { "id": 15, "tasks": ["10.2"] },
    { "id": 16, "tasks": ["10.3"] }
  ]
}
```
