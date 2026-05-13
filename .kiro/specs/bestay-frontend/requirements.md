# Requirements Document

## Introduction

This document defines the requirements for the Bestay Frontend — a Laravel Blade-based web interface for the Bestay hotel booking system. The frontend provides an Airbnb-inspired user experience for browsing rooms, making bookings, managing reservations, and administering the system. It uses Tailwind CSS for styling, Alpine.js for client-side interactivity, and Vite for asset bundling. Authentication is session-based (web guard).

## Glossary

- **Frontend**: The Blade template-based web interface rendered by Laravel web routes
- **Room_Listing_Page**: The homepage displaying available rooms in a card grid
- **Room_Detail_Page**: The page showing full details of a single room with booking capability
- **Booking_Flow**: The process of selecting dates, confirming details, and creating a booking
- **User_Dashboard**: The authenticated user's page showing their bookings and profile
- **Admin_Dashboard**: The admin-only interface for managing rooms and bookings
- **Design_System**: The Tailwind CSS configuration implementing the Airbnb-inspired visual language (Rausch accent, Inter font, soft corners)
- **Web_Controller**: Laravel controllers that render Blade views using session authentication
- **Alpine_Component**: Client-side Alpine.js components handling dynamic interactions

## Requirements

### Requirement 1: Design System and Layout Foundation

**User Story:** As a user, I want a visually consistent and responsive interface, so that I can comfortably browse and book rooms on any device.

#### Acceptance Criteria

1. THE Frontend SHALL use a white canvas (#ffffff) background with ink (#222222) text as the default color scheme
2. THE Frontend SHALL use Rausch (#ff385c) as the single accent color for all primary call-to-action elements
3. THE Frontend SHALL use Inter as the primary font family with system font fallbacks
4. THE Frontend SHALL apply soft rounded corners (8px for buttons, 14px for cards, pill-shaped for search elements)
5. THE Frontend SHALL implement a responsive layout with breakpoints at 744px (mobile), 1128px (tablet), and 1440px (desktop)
6. THE Frontend SHALL render a base layout with a top navigation bar, main content area, and footer
7. WHILE the viewport width is below 744px, THE Frontend SHALL collapse the navigation into a mobile-friendly hamburger menu
8. THE Frontend SHALL bundle all CSS and JavaScript assets through Vite

### Requirement 2: Navigation and Shared Components

**User Story:** As a user, I want clear navigation across all pages, so that I can easily move between browsing rooms, my bookings, and account settings.

#### Acceptance Criteria

1. THE Frontend SHALL display a top navigation bar with the Bestay logo, navigation links, and authentication state indicator on every page
2. WHILE the user is authenticated, THE Frontend SHALL show the user's name and links to Dashboard and Logout in the navigation
3. WHILE the user is not authenticated, THE Frontend SHALL show Login and Register links in the navigation
4. WHILE the user has admin role, THE Frontend SHALL show an Admin Dashboard link in the navigation
5. THE Frontend SHALL display a footer with site information and links on every page

### Requirement 3: Room Listing Page (Homepage)

**User Story:** As a visitor, I want to browse available rooms with filtering options, so that I can find a room that matches my preferences and budget.

#### Acceptance Criteria

1. WHEN a user visits the homepage, THE Room_Listing_Page SHALL display a grid of active room cards with image, name, type, capacity, and price per night
2. WHEN a user applies a room type filter, THE Room_Listing_Page SHALL display only rooms matching the selected type
3. WHEN a user applies a price range filter, THE Room_Listing_Page SHALL display only rooms within the specified minimum and maximum price
4. WHEN a user applies a capacity filter, THE Room_Listing_Page SHALL display only rooms with capacity greater than or equal to the selected value
5. THE Room_Listing_Page SHALL paginate results with 15 rooms per page
6. WHEN a user clicks on a room card, THE Room_Listing_Page SHALL navigate to the Room_Detail_Page for that room
7. THE Room_Listing_Page SHALL display room cards in a responsive grid (1 column on mobile, 2 on tablet, 3-4 on desktop)

### Requirement 4: Room Detail Page

**User Story:** As a visitor, I want to see complete room information and check availability, so that I can make an informed booking decision.

#### Acceptance Criteria

1. WHEN a user visits a room detail URL, THE Room_Detail_Page SHALL display the room's name, type, description, capacity, price per night, and image
2. THE Room_Detail_Page SHALL display a booking sidebar with date selection inputs for check-in and check-out
3. WHEN a user selects check-in and check-out dates, THE Alpine_Component SHALL call the availability API endpoint and display whether the room is available
4. WHEN the room is available for the selected dates, THE Room_Detail_Page SHALL display the total price calculated as (number of nights × price per night)
5. WHEN the room is not available for the selected dates, THE Room_Detail_Page SHALL display a clear unavailability message and disable the booking button
6. WHILE the user is not authenticated, THE Room_Detail_Page SHALL prompt the user to log in before booking
7. WHEN an authenticated user clicks the Book Now button with valid available dates, THE Booking_Flow SHALL create the booking and redirect to a confirmation view

### Requirement 5: Authentication Pages

**User Story:** As a visitor, I want to register and log in, so that I can make bookings and manage my reservations.

#### Acceptance Criteria

1. WHEN a user visits the login page, THE Frontend SHALL display a form with email and password fields and a submit button
2. WHEN a user submits valid login credentials, THE Frontend SHALL authenticate the user via session, and redirect to the homepage
3. WHEN a user submits invalid login credentials, THE Frontend SHALL display an error message without clearing the email field
4. WHEN a user visits the register page, THE Frontend SHALL display a form with name, email, password, and password confirmation fields
5. WHEN a user submits a valid registration form, THE Frontend SHALL create the account, authenticate the user, and redirect to the homepage
6. IF the registration email is already taken, THEN THE Frontend SHALL display a validation error indicating the email is in use
7. WHEN an authenticated user clicks Logout, THE Frontend SHALL end the session and redirect to the homepage

### Requirement 6: User Dashboard

**User Story:** As an authenticated user, I want to view and manage my bookings, so that I can track my reservations and cancel if needed.

#### Acceptance Criteria

1. WHEN an authenticated user visits the dashboard, THE User_Dashboard SHALL display a list of their bookings sorted by creation date (newest first)
2. THE User_Dashboard SHALL display each booking's room name, check-in date, check-out date, total price, and status
3. THE User_Dashboard SHALL visually distinguish booking statuses (pending, confirmed, cancelled, completed) using color-coded badges
4. WHEN a user clicks Cancel on a pending or confirmed booking, THE User_Dashboard SHALL cancel the booking and update the display
5. IF a user attempts to cancel an already cancelled or completed booking, THEN THE User_Dashboard SHALL display an error message
6. THE User_Dashboard SHALL paginate bookings with 15 items per page

### Requirement 7: Admin Dashboard - Room Management

**User Story:** As an admin, I want to manage rooms (create, edit, deactivate), so that I can maintain the hotel's room inventory.

#### Acceptance Criteria

1. WHEN an admin visits the admin rooms page, THE Admin_Dashboard SHALL display a table of all rooms with name, type, price, capacity, and active status
2. WHEN an admin clicks Create Room, THE Admin_Dashboard SHALL display a form with fields for name, type, description, price per night, capacity, and image URL
3. WHEN an admin submits a valid room creation form, THE Admin_Dashboard SHALL create the room and display it in the room list
4. WHEN an admin clicks Edit on a room, THE Admin_Dashboard SHALL display a pre-filled form for editing room details
5. WHEN an admin submits a valid room edit form, THE Admin_Dashboard SHALL update the room and reflect changes in the list
6. WHEN an admin clicks Deactivate on a room with no active bookings, THE Admin_Dashboard SHALL set the room as inactive
7. IF an admin attempts to deactivate a room with active bookings, THEN THE Admin_Dashboard SHALL display an error message explaining the room cannot be deactivated

### Requirement 8: Admin Dashboard - Booking Management

**User Story:** As an admin, I want to view and manage all bookings, so that I can confirm reservations, handle cancellations, and resolve conflicts.

#### Acceptance Criteria

1. WHEN an admin visits the admin bookings page, THE Admin_Dashboard SHALL display a paginated table of all bookings with guest name, room, dates, status, and total price
2. WHEN an admin filters bookings by status, THE Admin_Dashboard SHALL display only bookings matching the selected status
3. WHEN an admin clicks on a booking row, THE Admin_Dashboard SHALL display full booking details including guest information
4. WHEN an admin updates a booking status (confirm, cancel, complete), THE Admin_Dashboard SHALL apply the status change and refresh the display
5. IF an admin attempts an invalid status transition, THEN THE Admin_Dashboard SHALL display an error message
6. WHEN an admin views the conflicts page, THE Admin_Dashboard SHALL display all bookings with overlapping dates on the same room

### Requirement 9: Access Control

**User Story:** As a system operator, I want proper access control on all pages, so that users can only access features appropriate to their role.

#### Acceptance Criteria

1. WHEN an unauthenticated user attempts to access a protected page (dashboard, booking actions), THE Frontend SHALL redirect to the login page
2. WHEN a non-admin user attempts to access admin pages, THE Frontend SHALL return a 403 Forbidden response
3. THE Frontend SHALL use Laravel session-based authentication (web guard) for all protected routes
4. WHEN a user's session expires, THE Frontend SHALL redirect to the login page on the next request

### Requirement 10: Client-Side Interactivity

**User Story:** As a user, I want responsive and dynamic interactions, so that I can get immediate feedback without full page reloads.

#### Acceptance Criteria

1. WHEN a user interacts with date selection on the Room_Detail_Page, THE Alpine_Component SHALL validate that check-out is after check-in before calling the API
2. WHEN a user submits a form, THE Alpine_Component SHALL display inline validation errors returned by the server
3. WHEN an API call is in progress, THE Alpine_Component SHALL display a loading indicator on the relevant button or section
4. WHEN an API call fails due to network error, THE Alpine_Component SHALL display a user-friendly error message
5. THE Frontend SHALL use Alpine.js for all client-side state management and DOM manipulation without requiring a full SPA framework
