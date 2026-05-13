# Requirements Document

## Introduction

Bestay adalah sistem pemesanan kamar hotel/penginapan online yang dibangun menggunakan Laravel 12. Sistem ini menyediakan RESTful API untuk manajemen kamar, pembuatan booking dengan deteksi bentrok jadwal (conflict/clash detection), role-based access control (user & admin), autentikasi token via Laravel Sanctum, dan sistem notifikasi untuk konfirmasi booking serta perubahan status.

## Glossary

- **System**: Aplikasi Bestay secara keseluruhan
- **Booking_Service**: Service layer yang menangani business logic booking termasuk validasi bentrok
- **Auth_Controller**: Controller yang menangani registrasi, login, logout, dan manajemen token
- **Room_Controller**: Controller yang menangani operasi CRUD kamar hotel
- **Booking_Controller**: Controller yang menangani pembuatan dan pengelolaan booking oleh user
- **Admin_Booking_Controller**: Controller yang menangani pengelolaan semua booking oleh admin
- **Notification_Service**: Service yang mengirim notifikasi terkait status booking
- **User**: Pengguna dengan role 'user' yang dapat mencari kamar dan membuat booking
- **Admin**: Pengguna dengan role 'admin' yang dapat mengelola kamar dan semua booking
- **Booking**: Entitas pemesanan kamar yang memiliki status (pending, confirmed, cancelled, completed)
- **Room**: Entitas kamar hotel dengan atribut tipe, harga, kapasitas, dan status aktif
- **Conflict**: Kondisi dimana dua booking aktif pada kamar yang sama memiliki rentang tanggal yang overlap
- **Active_Booking**: Booking dengan status 'pending' atau 'confirmed'
- **Sanctum_Token**: Token autentikasi yang diterbitkan oleh Laravel Sanctum untuk akses API

## Requirements

### Requirement 1: User Registration

**User Story:** As a new user, I want to register an account, so that I can access the booking system and make reservations.

#### Acceptance Criteria

1. WHEN a user submits a registration request with valid name (1 to 255 characters), a valid email address (RFC 5322 format, maximum 255 characters), a password (minimum 8 characters, maximum 128 characters), and a matching password_confirmation, THE Auth_Controller SHALL create a new user account with role 'user', store the password in hashed form, and return a 201 status code with the user data (id, name, email, role, created_at) and a Sanctum_Token
2. WHEN a user submits a registration request with an email that already exists in the system, THE Auth_Controller SHALL reject the request with a 422 status code and a validation error message indicating the email is already taken
3. WHEN a user submits a registration request with a password shorter than 8 characters, THE Auth_Controller SHALL reject the request with a 422 status code and a validation error message indicating the password length requirement
4. WHEN a user submits a registration request with mismatched password and password_confirmation, THE Auth_Controller SHALL reject the request with a 422 status code and a validation error message indicating the passwords do not match
5. IF a user submits a registration request with any required field (name, email, password, password_confirmation) missing or empty, THEN THE Auth_Controller SHALL reject the request with a 422 status code and a validation error message indicating which fields are required
6. IF a user submits a registration request with an email that is not in valid RFC 5322 format, THEN THE Auth_Controller SHALL reject the request with a 422 status code and a validation error message indicating the email format is invalid

### Requirement 2: User Authentication

**User Story:** As a registered user, I want to log in and out of the system, so that I can securely access my bookings and profile.

#### Acceptance Criteria

1. WHEN a user submits a login request with a registered email and correct password, THE Auth_Controller SHALL authenticate the user and return a JSON response containing the Sanctum_Token and the user's id, name, and email
2. IF a user submits a login request with an unregistered email or incorrect password, THEN THE Auth_Controller SHALL reject the request with a 401 status code and a response body containing an error message indicating invalid credentials
3. IF a user submits a login request with a missing or empty email or password field, THEN THE Auth_Controller SHALL reject the request with a 422 status code and a response body containing validation error messages for each invalid field
4. WHEN an authenticated user requests logout, THE Auth_Controller SHALL revoke the current Sanctum_Token and return a confirmation response with a 200 status code
5. WHEN an authenticated user requests their profile, THE Auth_Controller SHALL return a JSON response containing the user's id, name, email, and email_verified_at fields
6. IF a request is made without a valid Sanctum_Token to a protected endpoint (POST /api/logout, GET /api/profile), THEN THE System SHALL return a 401 Unauthorized response with a JSON error message indicating the user is unauthenticated

### Requirement 3: Room Management

**User Story:** As an admin, I want to manage hotel rooms (create, read, update, delete), so that I can maintain the room inventory available for booking.

#### Acceptance Criteria

1. WHEN any user requests the room list, THE Room_Controller SHALL return a paginated list of active rooms with a default page size of 15 items per page
2. WHEN any user requests room details by ID, THE Room_Controller SHALL return the complete room information including all attributes (name, type, description, price_per_night, capacity, image_url, is_active)
3. WHEN an admin submits valid room data (name max 255 characters, type one of standard/deluxe/suite, description nullable text, price_per_night decimal minimum 0, capacity integer minimum 1), THE Room_Controller SHALL create a new room with is_active defaulting to true and return a 201 status code
4. WHEN an admin submits an update request for an existing room with valid data, THE Room_Controller SHALL update the room attributes and return the updated room
5. WHEN an admin requests deletion of a room that has no Active_Booking, THE Room_Controller SHALL set the room's is_active attribute to false (soft delete) and return a success response
6. IF a non-admin user attempts to create, update, or delete a room, THEN THE System SHALL return a 403 Forbidden response
7. WHEN a user requests the room list with filters (type, min_price, max_price, capacity), THE Room_Controller SHALL return only active rooms matching all provided filter criteria
8. IF a user requests a room by ID that does not exist, THEN THE Room_Controller SHALL return a 404 Not Found response
9. IF an admin submits room data that fails validation, THEN THE Room_Controller SHALL return a 422 status code with error messages indicating which fields failed validation
10. IF an admin requests deletion of a room that has at least one Active_Booking, THEN THE Room_Controller SHALL reject the request with a 409 Conflict response indicating the room has active bookings

### Requirement 4: Room Availability Check

**User Story:** As a user, I want to check room availability for specific dates, so that I can find available rooms before making a booking.

#### Acceptance Criteria

1. WHEN an authenticated user requests availability for a room with check_in and check_out dates in YYYY-MM-DD format where check_in is today or later and check_out is after check_in, THE Room_Controller SHALL return whether the room is available for that date range
2. WHEN a room has no Active_Booking overlapping with the requested date range, THE Booking_Service SHALL report the room as available
3. WHEN a room has at least one Active_Booking overlapping with the requested date range, THE Booking_Service SHALL report the room as unavailable
4. IF the check_in date is in the past or the check_out date is on or before the check_in date or either date is missing or not in YYYY-MM-DD format, THEN THE Room_Controller SHALL reject the request with a 422 validation error indicating which date parameter is invalid
5. IF the requested room does not exist, THEN THE Room_Controller SHALL return a 404 Not Found response

### Requirement 5: Booking Creation with Conflict Detection

**User Story:** As a user, I want to create a booking for an available room, so that I can reserve accommodation for my desired dates.

#### Acceptance Criteria

1. WHEN an authenticated user submits a valid booking request (room_id, check_in, check_out, and optional notes), THE Booking_Service SHALL check for conflicts and create a booking with status 'confirmed' if no conflict exists, returning the booking details including id, room_id, user_id, check_in, check_out, total_price, status, and notes
2. WHEN a booking is successfully created, THE Booking_Service SHALL calculate total_price as the number of nights (check_out date minus check_in date) multiplied by the room's price_per_night
3. WHEN a booking request conflicts with an existing Active_Booking on the same room, THE Booking_Service SHALL reject the request with a 409 Conflict response including the conflicting booking's id, check_in, and check_out dates
4. THE Booking_Service SHALL detect a conflict when the new booking's check_in is before an existing booking's check_out AND the new booking's check_out is after an existing booking's check_in, considering only Active_Bookings (status 'pending' or 'confirmed')
5. WHEN a booking request targets a room that is not active (is_active = false), THE Booking_Service SHALL reject the request with a 422 error indicating the room is not available
6. WHEN a booking request has check_in date in the past (before today's date), THE System SHALL reject the request with a 422 validation error
7. WHEN a booking request has check_out date on or before check_in date, THE System SHALL reject the request with a 422 validation error
8. THE Booking_Service SHALL use a database transaction to prevent race conditions during conflict detection and booking creation
9. IF a booking request references a room_id that does not exist, THEN THE System SHALL reject the request with a 404 error indicating the room was not found
10. IF a booking request includes a notes field exceeding 500 characters, THEN THE System SHALL reject the request with a 422 validation error

### Requirement 6: Booking Management by User

**User Story:** As a user, I want to view and cancel my bookings, so that I can manage my reservations.

#### Acceptance Criteria

1. WHEN an authenticated user requests their booking list, THE Booking_Controller SHALL return a paginated list containing only bookings belonging to that user, sorted by creation date descending
2. WHEN an authenticated user requests details of their own booking, THE Booking_Controller SHALL return the booking information including booking fields (id, room_id, user_id, check_in, check_out, total_price, status, notes, created_at) and the associated room details
3. IF a user attempts to view a booking that belongs to another user, THEN THE System SHALL return a 403 Forbidden response
4. WHEN a user requests cancellation of their own booking with status 'pending' or 'confirmed', THE Booking_Controller SHALL update the booking status to 'cancelled'
5. IF a user attempts to cancel a booking with status 'cancelled' or 'completed', THEN THE System SHALL reject the request with a 422 response and an error message indicating the booking cannot be cancelled in its current status
6. IF a user requests details of a booking that does not exist, THEN THE System SHALL return a 404 Not Found response

### Requirement 7: Admin Booking Management

**User Story:** As an admin, I want to view all bookings and manage their statuses, so that I can oversee hotel operations and handle booking issues.

#### Acceptance Criteria

1. WHEN an admin requests the booking list, THE Admin_Booking_Controller SHALL return a paginated list of all bookings from all users with a default page size of 15 items per page
2. WHEN an admin requests details of any booking, THE Admin_Booking_Controller SHALL return the complete booking information including id, user_id, room_id, check_in, check_out, total_price, status, notes, and timestamps
3. WHEN an admin submits a valid status transition (pending to confirmed, pending to cancelled, confirmed to cancelled, or confirmed to completed), THE Admin_Booking_Controller SHALL update the booking status and return the updated booking
4. THE System SHALL enforce valid status transitions: pending to confirmed or cancelled, confirmed to cancelled or completed
5. IF an admin attempts to change the status of a booking with a terminal status ('cancelled' or 'completed') or submits an invalid transition, THEN THE System SHALL reject the request with a 422 error indicating the transition is not allowed
6. WHEN an admin requests the conflicts list, THE Admin_Booking_Controller SHALL return all Active_Bookings where two or more bookings on the same room have overlapping date ranges (check_in before the other's check_out AND check_out after the other's check_in)
7. IF a non-admin user attempts to access admin booking endpoints, THEN THE System SHALL return a 403 Forbidden response
8. IF an admin requests details of a booking that does not exist, THEN THE System SHALL return a 404 Not Found response

### Requirement 8: Notification System

**User Story:** As a user, I want to receive notifications about my booking status, so that I am informed about confirmations, cancellations, and status changes.

#### Acceptance Criteria

1. WHEN a booking is successfully created, THE Notification_Service SHALL create a notification record for the booking owner with type 'booking_confirmed', storing the associated booking_id, a title, and a message
2. WHEN a booking is cancelled by the owning user, THE Notification_Service SHALL create a notification record for that user with type 'booking_cancelled'
3. WHEN an admin changes a booking status, THE Notification_Service SHALL create a notification record for the booking owner with type 'status_updated' indicating the new status
4. WHEN an authenticated user requests their notifications via GET /api/notifications, THE System SHALL return a paginated list of notifications belonging to that user, ordered by creation date descending (newest first)
5. WHEN a user sends a PATCH request to mark a notification as read, THE System SHALL set the notification's is_read field to true and set read_at to the current timestamp
6. IF a user attempts to mark a notification as read that is already read, THEN THE System SHALL return the notification unchanged with a success response
7. IF a user attempts to access or modify a notification that does not belong to them, THEN THE System SHALL return a 403 Forbidden response
8. WHEN a user marks all notifications as read via POST /api/notifications/read-all, THE System SHALL update all unread notifications for that user setting is_read to true and read_at to the current timestamp, and return the count of updated notifications
9. IF a user requests a notification that does not exist, THEN THE System SHALL return a 404 Not Found response

### Requirement 9: Data Integrity and Validation

**User Story:** As a system operator, I want all data to be validated and consistent, so that the system maintains reliable booking records.

#### Acceptance Criteria

1. THE System SHALL validate that every booking has check_in date strictly before check_out date
2. THE System SHALL validate that room type is one of: standard, deluxe, suite
3. THE System SHALL validate that price_per_night is a numeric value between 0 and 99,999,999.99 inclusive
4. THE System SHALL validate that room capacity is an integer between 1 and 100 inclusive
5. THE System SHALL maintain the invariant that total_price equals the number of nights (difference in days between check_in and check_out) multiplied by the room's price_per_night at time of booking
6. THE System SHALL ensure that no two Active_Bookings for the same room have overlapping date ranges (No Double Booking invariant)
7. IF any validation rule defined in criteria 1 through 4 is violated during a create or update request, THEN THE System SHALL reject the request with a 422 status code and return a response body indicating which fields failed validation

### Requirement 10: API Security

**User Story:** As a system operator, I want the API to be secure, so that user data and booking operations are protected from unauthorized access.

#### Acceptance Criteria

1. THE System SHALL require a valid Sanctum_Token for all API endpoints except registration and login
2. THE System SHALL use Laravel Policies to enforce that users can only access their own bookings and notifications, and that only Admin users can create, update, or delete rooms
3. THE System SHALL validate all input through FormRequest classes before processing, and IF validation fails, THEN THE System SHALL return a 422 response containing the validation error messages
4. THE System SHALL apply rate limiting of 5 requests per minute on authentication endpoints (login and registration)
5. IF a client exceeds the rate limit on authentication endpoints, THEN THE System SHALL return a 429 Too Many Requests response with a Retry-After header indicating the number of seconds until the limit resets
6. THE System SHALL use Eloquent ORM with parameterized queries to prevent SQL injection
