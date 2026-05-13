# Implementation Plan: Bestay Frontend

## Overview

Build the Bestay frontend as Laravel Blade templates with Tailwind CSS styling and Alpine.js interactivity. The implementation follows a bottom-up approach: design system foundation first, then shared layout/components, then individual pages (public → authenticated → admin), and finally wiring everything together with routes and controllers.

## Tasks

- [x] 1. Set up frontend tooling and design system foundation
  - [x] 1.1 Configure Tailwind CSS with the Bestay design tokens
    - Update `resources/css/app.css` to import Tailwind and define custom CSS variables for the design system (colors: Rausch #ff385c, ink #222222, canvas #ffffff; font: Inter; border-radius tokens: 8px buttons, 14px cards, 9999px pills; spacing scale)
    - Install Alpine.js via npm and register it in `resources/js/app.js`
    - Update `vite.config.js` if needed to ensure Blade template paths are scanned by Tailwind
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.8_

  - [x] 1.2 Create the base Blade layout (`resources/views/layouts/app.blade.php`)
    - Include `@vite` directives for CSS and JS
    - Define `@yield('content')` section for page content
    - Include the navigation component and footer component
    - Set up responsive meta viewport tag
    - _Requirements: 1.5, 1.6_

- [x] 2. Build shared Blade components (navigation, footer, UI elements)
  - [x] 2.1 Create the navigation component (`resources/views/components/navbar.blade.php`)
    - Display Bestay logo/brand name linking to homepage
    - Conditionally show authenticated user's name, Dashboard link, Logout button (using `@auth`)
    - Conditionally show Login/Register links for guests (using `@guest`)
    - Conditionally show Admin Dashboard link for admin users (`@if(auth()->user()->isAdmin())`)
    - Implement responsive hamburger menu for mobile (< 744px) using Alpine.js `x-data` toggle
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 1.7_

  - [x] 2.2 Create the footer component (`resources/views/components/footer.blade.php`)
    - Display site information, copyright, and relevant links
    - Style with design system tokens
    - _Requirements: 2.5_

  - [x] 2.3 Create reusable UI Blade components
    - Create `components/room-card.blade.php` — displays room image, name, type, capacity, price with Airbnb-style card design (14px rounded corners, shadow on hover)
    - Create `components/status-badge.blade.php` — renders color-coded badge based on booking status (pending=yellow, confirmed=green, cancelled=red, completed=gray)
    - Create `components/pagination.blade.php` — styled pagination links matching design system
    - _Requirements: 3.1, 6.3_

- [x] 3. Checkpoint - Verify foundation
  - Ensure `npm run build` completes without errors, verify Tailwind classes compile, and Alpine.js loads. Ask the user if questions arise.

- [x] 4. Implement authentication pages and web auth flow
  - [x] 4.1 Create the web AuthController (`app/Http/Controllers/Web/AuthController.php`)
    - `showLogin()` — renders login Blade view
    - `login()` — validates credentials, authenticates via `Auth::attempt()`, redirects to homepage on success, back with errors on failure
    - `showRegister()` — renders register Blade view
    - `register()` — validates input, creates user, logs in via `Auth::login()`, redirects to homepage
    - `logout()` — calls `Auth::logout()`, invalidates session, redirects to homepage
    - _Requirements: 5.2, 5.3, 5.5, 5.6, 5.7_

  - [x] 4.2 Create login view (`resources/views/auth/login.blade.php`)
    - Form with email and password fields, submit button styled with Rausch primary CTA
    - Display validation errors inline using `@error` directive
    - Preserve old email value on failed submission using `old('email')`
    - Link to register page
    - _Requirements: 5.1, 5.3_

  - [x] 4.3 Create register view (`resources/views/auth/register.blade.php`)
    - Form with name, email, password, password_confirmation fields
    - Display validation errors inline
    - Link to login page
    - _Requirements: 5.4, 5.5, 5.6_

  - [ ]* 4.4 Write feature tests for web authentication
    - Test login with valid credentials redirects to homepage
    - Test login with invalid credentials shows error
    - Test registration creates user and logs in
    - Test duplicate email shows validation error
    - Test logout ends session
    - _Requirements: 5.2, 5.3, 5.5, 5.6, 5.7_

- [x] 5. Implement Room Listing Page (Homepage)
  - [x] 5.1 Create the web RoomController (`app/Http/Controllers/Web/RoomController.php`)
    - `index()` — queries active rooms with optional filters (type, min_price, max_price, capacity), paginates at 15, passes to Blade view
    - `show(Room $room)` — loads room details, passes to Blade view
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 4.1_

  - [x] 5.2 Create room listing view (`resources/views/rooms/index.blade.php`)
    - Filter bar with dropdowns/inputs for type, price range, capacity (using Alpine.js for dynamic filter submission)
    - Responsive grid of room cards (1-col mobile, 2-col tablet, 3-4 col desktop using Tailwind grid classes)
    - Each card links to room detail page
    - Pagination component at bottom
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7_

  - [ ]* 5.3 Write property test for room filtering
    - **Property 1: Room filter correctness**
    - **Validates: Requirements 3.2, 3.3, 3.4**

  - [ ]* 5.4 Write property test for pagination bounds
    - **Property 2: Pagination bounds**
    - **Validates: Requirements 3.5, 6.6**

- [x] 6. Implement Room Detail Page and Booking Flow
  - [x] 6.1 Create room detail view (`resources/views/rooms/show.blade.php`)
    - Display room name, type, description, capacity, price per night, and image (full-width hero style)
    - Booking sidebar with Alpine.js component: date inputs for check-in/check-out, availability check button, total price display, Book Now button
    - Alpine.js logic: validate dates (check-out > check-in), fetch availability from `/api/rooms/{id}/availability`, calculate and display total price, handle unavailable state
    - Show login prompt for unauthenticated users instead of booking form
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

  - [x] 6.2 Create web BookingController (`app/Http/Controllers/Web/BookingController.php`)
    - `store(Request $request)` — validates dates, calls BookingService to create booking, redirects to dashboard with success message
    - Handle conflict (409) by redirecting back with error
    - _Requirements: 4.7_

  - [ ]* 6.3 Write property test for total price calculation
    - **Property 3: Total price calculation**
    - **Validates: Requirements 4.4**

  - [ ]* 6.4 Write property test for date validation
    - **Property 7: Date validation prevents invalid ranges**
    - **Validates: Requirements 10.1**

- [x] 7. Checkpoint - Verify public pages
  - Ensure all tests pass, verify room listing and detail pages render correctly with test data. Ask the user if questions arise.

- [x] 8. Implement User Dashboard
  - [x] 8.1 Create web DashboardController (`app/Http/Controllers/Web/DashboardController.php`)
    - `index()` — queries authenticated user's bookings with room relationship, sorted by created_at desc, paginated at 15, passes to view
    - `cancelBooking(Booking $booking)` — authorizes via policy, calls BookingService to cancel, redirects back with message
    - _Requirements: 6.1, 6.4, 6.5, 6.6_

  - [x] 8.2 Create dashboard view (`resources/views/dashboard/index.blade.php`)
    - List of booking cards/rows showing room name, check-in, check-out, total price, status badge
    - Cancel button on pending/confirmed bookings (with Alpine.js confirmation dialog)
    - Pagination component
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.6_

  - [ ]* 8.3 Write property test for booking sort order
    - **Property 4: Booking sort order**
    - **Validates: Requirements 6.1**

  - [ ]* 8.4 Write property test for status badge uniqueness
    - **Property 5: Status badge uniqueness**
    - **Validates: Requirements 6.3**

- [x] 9. Implement Admin Dashboard - Room Management
  - [x] 9.1 Create web AdminRoomController (`app/Http/Controllers/Web/AdminRoomController.php`)
    - `index()` — queries all rooms (active and inactive), passes to view
    - `create()` — renders room creation form
    - `store(StoreRoomRequest $request)` — creates room, redirects to admin rooms list
    - `edit(Room $room)` — renders pre-filled edit form
    - `update(UpdateRoomRequest $request, Room $room)` — updates room, redirects to admin rooms list
    - `destroy(Room $room)` — deactivates room (checks for active bookings), redirects with success/error message
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7_

  - [x] 9.2 Create admin room views
    - `resources/views/admin/rooms/index.blade.php` — table of all rooms with name, type, price, capacity, active status, Edit/Deactivate action buttons
    - `resources/views/admin/rooms/create.blade.php` — form with all room fields
    - `resources/views/admin/rooms/edit.blade.php` — pre-filled form with current room values
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [x] 10. Implement Admin Dashboard - Booking Management
  - [x] 10.1 Create web AdminBookingController (`app/Http/Controllers/Web/AdminBookingController.php`)
    - `index()` — queries all bookings with user/room relationships, optional status filter, paginated at 15
    - `show(Booking $booking)` — loads booking with user and room, renders detail view
    - `updateStatus(Request $request, Booking $booking)` — validates status transition via BookingService, redirects with success/error
    - `conflicts()` — queries conflicting bookings, renders conflicts view
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6_

  - [x] 10.2 Create admin booking views
    - `resources/views/admin/bookings/index.blade.php` — table with guest name, room, dates, status badge, total price; status filter dropdown; pagination
    - `resources/views/admin/bookings/show.blade.php` — full booking details with status update buttons (Confirm, Cancel, Complete)
    - `resources/views/admin/bookings/conflicts.blade.php` — list of conflicting bookings grouped by room
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.6_

  - [ ]* 10.3 Write property test for admin booking status filter
    - **Property 6: Admin booking status filter**
    - **Validates: Requirements 8.2**

- [x] 11. Wire up routes and middleware
  - [x] 11.1 Define all web routes in `routes/web.php`
    - Public routes: homepage (rooms.index), room detail (rooms.show), login, register
    - Auth routes (middleware `auth`): logout, dashboard, booking store, booking cancel
    - Admin routes (middleware `auth` + `admin`): admin rooms CRUD, admin bookings index/show/updateStatus/conflicts
    - _Requirements: 9.1, 9.2, 9.3, 9.4_

  - [x] 11.2 Create web-compatible AdminMiddleware for Blade responses
    - Adapt existing AdminMiddleware to redirect to a 403 error page (or homepage with flash message) instead of returning JSON for web requests
    - Ensure `auth` middleware redirects unauthenticated users to login page
    - _Requirements: 9.1, 9.2_

- [x] 12. Implement Alpine.js interactive components
  - [x] 12.1 Build the availability checker Alpine component
    - Date validation (check-out must be after check-in)
    - Fetch availability from API with loading state
    - Calculate and display total price on success
    - Display unavailability message and disable booking button on conflict
    - Display network error messages gracefully
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

  - [x] 12.2 Build form submission Alpine components
    - Loading state on submit buttons during form submission
    - Inline validation error display from server responses
    - Confirmation dialog for destructive actions (cancel booking, deactivate room)
    - _Requirements: 10.2, 10.3, 10.4_

  - [ ]* 12.3 Write property test for validation error display
    - **Property 8: Validation error display completeness**
    - **Validates: Requirements 10.2**

- [x] 13. Final checkpoint - Full integration verification
  - Ensure all tests pass, verify all pages render correctly, navigation works across all roles (guest, user, admin), and booking flow completes end-to-end. Ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties from the design document
- The existing API controllers remain unchanged — new Web controllers handle Blade rendering with session auth
- Alpine.js calls the existing API endpoints for dynamic operations (availability check)
