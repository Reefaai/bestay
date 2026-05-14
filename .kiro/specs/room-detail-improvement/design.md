# Design Document: Room Detail Improvement

## Overview

This design covers the visual and UX improvements to the Bestay room detail page (`rooms/show.blade.php`). The current page has a functional structure with hero image, description, amenities, ratings, policies, and a booking sidebar. This redesign enhances the page with a polished image gallery, room highlights section, improved amenity display with expand/collapse, an embedded Leaflet.js map, a premium booking sidebar with price breakdown, micro-interactions (fade-in animations, sticky sidebar), responsive mobile optimizations, and improved typography hierarchy.

The implementation stays within the existing Laravel + Blade + Tailwind CSS + Alpine.js stack. Leaflet.js is the only new external dependency, loaded via CDN for the map section.

## Architecture

The room detail page follows a server-rendered Blade template architecture with client-side interactivity provided by Alpine.js. No new backend routes or controllers are needed — all improvements are frontend-only, modifying the existing `rooms/show.blade.php` template and the `Web\RoomController@show` method (to pass additional data like highlights and map coordinates).

```mermaid
graph TD
    A[Web\RoomController@show] --> B[rooms/show.blade.php]
    B --> C[Image Gallery Section]
    B --> D[Room Highlights Section]
    B --> E[Description Section]
    B --> F[Amenity Section with Expand/Collapse]
    B --> G[Map Section - Leaflet.js]
    B --> H[Ratings Section]
    B --> I[Policies Section]
    B --> J[Booking Sidebar]
    
    J --> K[Alpine.js: Date Picker + Availability Check]
    F --> L[Alpine.js: Expand/Collapse Toggle]
    C --> M[CSS: Hover Zoom Animation]
    B --> N[CSS: Fade-in Staggered Animation]
    J --> O[CSS: Sticky Positioning]
    G --> P[Leaflet.js CDN: Interactive Map]
```

### Key Architectural Decisions

1. **No SPA framework** — All interactivity uses Alpine.js inline directives, consistent with the existing codebase.
2. **Leaflet.js via CDN** — Avoids adding a build dependency; loaded conditionally only on the room detail page via `@push('scripts')`.
3. **Highlights data in controller** — Room highlights are defined as a static mapping in the controller (similar to existing amenities pattern), avoiding database schema changes.
4. **CSS animations via Tailwind utilities + custom keyframes** — Fade-in and stagger animations use a small set of custom CSS keyframes added to the app stylesheet.
5. **Mobile-first responsive approach** — Base styles target mobile, with `md:` and `lg:` breakpoints for tablet and desktop.

## Components and Interfaces

### 1. Image Gallery Component

**Location:** Inline in `rooms/show.blade.php` (hero section)

**Behavior:**
- Displays room image in a rounded container with 12px border-radius
- Hover triggers a 1.05× scale animation (300ms transition, overflow hidden)
- Responsive aspect ratio: 16:9 on ≥768px, 4:3 on <768px
- Placeholder with gradient background (gray-200 to gray-300) and camera icon when no image

### 2. Room Highlights Component

**Location:** Between room meta section and description section

**Data Source:** Static mapping in `RoomController@show`, keyed by room type

```php
$highlightsByType = [
    'standard' => [
        ['icon' => 'bed', 'title' => 'Tempat Tidur Nyaman', 'description' => '1 Queen Bed dengan bantal premium'],
        ['icon' => 'size', 'title' => '24 m²', 'description' => 'Ruangan yang cukup luas'],
        ['icon' => 'view', 'title' => 'Pemandangan Taman', 'description' => 'Jendela menghadap area hijau'],
    ],
    'deluxe' => [...],
    'suite' => [...],
    'family' => [...],
];
```

**Rendering Rules:**
- Displays 3–4 highlight cards per room type
- Each card: icon + bold title (max 30 chars) + description (max 80 chars)
- Not rendered if highlight data is unavailable

### 3. Amenity Section with Expand/Collapse

**Location:** Existing amenity section, enhanced

**Behavior (Alpine.js):**
- Shows first 6 amenities by default
- "Tampilkan semua fasilitas" button appears when >6 amenities
- Click expands remaining amenities with 300ms animation
- Button label toggles to "Sembunyikan"
- Each amenity displayed in a rounded card with background fill and distinct icon

**Icon Mapping:** A PHP array maps amenity icon keys to SVG paths (wifi → WiFi icon, ac → snowflake, tv → TV icon, etc.)

### 4. Map Section (Leaflet.js)

**Location:** Below amenities section

**Data Source:** Hotel coordinates passed from controller (hardcoded for now, since all rooms are in the same hotel)

```php
$mapData = [
    'lat' => -6.2088,   // Jakarta default
    'lng' => 106.8456,
    'name' => 'Bestay Hotel',
    'area' => 'Jakarta Pusat, DKI Jakarta',
    'zoom' => 15,
];
```

**Behavior:**
- Interactive Leaflet map with OpenStreetMap tiles
- Pin marker at hotel coordinates
- Popup with hotel name on marker click
- Container: 300px height (≥744px), 200px (<744px), 8px border-radius
- Fallback placeholder if tiles fail to load (static, no interactive features)

### 5. Booking Sidebar (Enhanced)

**Location:** Existing sidebar, enhanced styling

**Enhancements:**
- Price displayed at 1.5× base font size with "per malam" label
- Container with visible border + drop shadow for elevation
- Price breakdown: nightly rate × nights line item + total with horizontal divider
- Trust indicator text below booking button ("Konfirmasi instan")
- Disabled state with reduced opacity when room unavailable or dates are invalid (negative nights, check-out before check-in)
- Sticky positioning on desktop (top: 24px offset), non-sticky on mobile

### 6. Mobile Bottom Bar

**Location:** Fixed bottom bar, visible only on <768px viewports

**Behavior:**
- Shows per-night price + "Pesan" button
- Max height 72px
- Click scrolls to full booking form section
- Page has bottom padding ≥ bar height to prevent content occlusion

### 7. Fade-in Animation System

**Implementation:** CSS keyframes + utility classes

```css
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in-up {
    animation: fadeInUp 300ms ease-out forwards;
    opacity: 0;
}
```

Each major section gets a stagger delay via inline style: `animation-delay: ${index * 100}ms`.

## Data Models

No database schema changes are required. All new data (highlights, map coordinates) is defined as static configuration in the controller.

### Data Passed to View (Updated)

```php
// Existing
compact('room', 'amenities', 'policies', 'rating')

// New additions
compact('room', 'amenities', 'policies', 'rating', 'highlights', 'mapData')
```

### Highlights Structure

```php
// Array of highlight items
[
    'icon' => string,        // Icon identifier (bed, size, view, wifi, etc.)
    'title' => string,       // Max 30 characters
    'description' => string, // Max 80 characters
]
```

### Map Data Structure

```php
[
    'lat' => float,    // Latitude
    'lng' => float,    // Longitude
    'name' => string,  // Hotel name for marker popup
    'area' => string,  // Area description (max 100 chars)
    'zoom' => int,     // Default zoom level (15)
]
```

## Error Handling

| Scenario | Handling |
|----------|----------|
| Room has no image | Display gradient placeholder with camera icon |
| Highlights not defined for room type | Do not render highlights section (no empty container) |
| Map tiles fail to load | Display static placeholder with "Peta tidak tersedia" message, no interactive features (no zoom, no markers, no popups) |
| Availability check network error | Show error message in sidebar (existing behavior) |
| Room unavailable for dates | Disable booking button, hide price breakdown (existing + enhanced styling) |
| Invalid date selection (negative nights, check-out before check-in) | Disable booking button, hide price breakdown |
| JavaScript disabled | Page renders server-side content; map section shows placeholder; amenity expand/collapse shows all amenities by default |

## Testing Strategy

### Why Property-Based Testing Does Not Apply

This feature is entirely focused on **UI rendering, layout, and visual styling**. The changes involve:
- Blade template modifications (HTML structure)
- Tailwind CSS classes and custom CSS animations
- Alpine.js interactive behaviors (expand/collapse, sticky positioning)
- Leaflet.js map integration (external library)

There are no pure functions, data transformations, parsers, serializers, or business logic algorithms being introduced. All acceptance criteria relate to visual appearance, responsive breakpoints, animation timings, and UI interactions — none of which are suitable for property-based testing.

### Recommended Testing Approach

**1. Visual/Manual Testing**
- Verify image gallery hover effects and aspect ratios across breakpoints
- Verify highlights section renders correctly per room type
- Verify amenity expand/collapse animation behavior
- Verify map renders with correct marker and popup
- Verify booking sidebar sticky behavior and price breakdown
- Verify mobile bottom bar and responsive stacking

**2. Browser Testing (Cross-device)**
- Desktop (≥1128px): Sticky sidebar, 16:9 image, full layout
- Tablet (768px–1127px): Non-sticky sidebar, 16:9 image, two-column
- Mobile (<768px): Stacked layout, 4:3 image, fixed bottom bar, single-column amenities

**3. Example-Based Unit Tests (Blade/Controller)**
- Test that `RoomController@show` passes `highlights` data for each room type
- Test that `RoomController@show` passes `mapData` with required keys
- Test that highlights are not passed when room type has no defined highlights
- Test amenity count logic (>6 triggers expand button)

**4. Integration Tests**
- HTTP test: GET `/rooms/{id}` returns 200 and contains expected HTML elements
- Verify breadcrumb structure in response
- Verify map container is present in response HTML
- Verify highlights section renders for known room types

**5. Accessibility Testing**
- All interactive elements have minimum 44×44px touch targets on mobile
- Map has appropriate ARIA labels
- Expand/collapse button has `aria-expanded` attribute
- Images have alt text
- Color contrast meets WCAG AA for all text elements
