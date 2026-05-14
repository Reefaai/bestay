# Implementation Plan: Room Detail Improvement

## Overview

Enhance the Bestay room detail page (`rooms/show.blade.php`) with improved image gallery, room highlights, better amenity display with expand/collapse, embedded Leaflet.js map, polished booking sidebar, micro-interactions (fade-in animations, sticky sidebar), responsive mobile optimizations, and improved typography. All changes are frontend-focused using the existing Laravel + Blade + Tailwind CSS + Alpine.js stack, with Leaflet.js as the only new dependency (via CDN).

## Tasks

- [x] 1. Update controller and add custom CSS animations
  - [x] 1.1 Add highlights and map data to RoomController@show
    - Add `$highlightsByType` static array mapping room types to highlight items (icon, title, description)
    - Add `$mapData` array with hotel coordinates (lat, lng, name, area, zoom)
    - Pass `highlights` and `mapData` to the view via `compact()`
    - _Requirements: 2.1, 2.4, 4.1, 4.2, 4.3_

  - [x] 1.2 Add custom CSS keyframes for fade-in animation
    - Add `@keyframes fadeInUp` to the app stylesheet (from opacity:0/translateY(16px) to opacity:1/translateY(0))
    - Create `.animate-fade-in-up` utility class with 300ms ease-out animation
    - Ensure the animation uses `forwards` fill mode and starts with `opacity: 0`
    - _Requirements: 6.1_

- [x] 2. Implement image gallery and typography improvements
  - [x] 2.1 Redesign the hero image section
    - Change container to use `rounded-xl` (12px border-radius) with `overflow-hidden`
    - Add hover effect: CSS transition 300ms, scale to 1.05× on hover with `overflow-hidden` on container
    - Set responsive aspect ratio: `aspect-[16/9]` on `md:` and above, `aspect-[4/3]` on mobile
    - Update placeholder to use gradient background (`from-gray-200 to-gray-300`) with centered camera icon
    - _Requirements: 1.1, 1.2, 1.3, 1.4_

  - [x] 2.2 Improve breadcrumb navigation
    - Replace slash (`/`) separators with chevron (`›`) characters
    - Add `hover:text-ink` with `transition-colors duration-200` on clickable breadcrumb items
    - _Requirements: 6.4_

  - [x] 2.3 Apply typography hierarchy improvements
    - Set room name heading to `text-[28px]` on `md:` and above, `text-[22px]` on mobile
    - Set room description paragraph `leading-[1.6]` line height
    - Style all section headings with `text-[20px] font-semibold mb-6` (24px bottom margin)
    - Add room type badge with colored backgrounds: gold for suite, blue for deluxe, green for family, gray for standard (enforce that badge colors must match their corresponding room types)
    - _Requirements: 8.1, 8.2, 8.3, 8.4_

  - [ ]* 2.4 Write unit tests for RoomController highlights and map data
    - Test that `show()` passes `highlights` array for each room type (standard, deluxe, suite, family)
    - Test that `show()` passes `mapData` with required keys (lat, lng, name, area, zoom)
    - Test that highlights are not passed when room type has no defined highlights
    - _Requirements: 2.1, 2.4, 4.2_

- [x] 3. Implement room highlights section
  - [x] 3.1 Create room highlights section in the Blade template
    - Add highlights section between room meta (capacity/price/bed) and description section
    - Render 3–4 highlight cards with icon, bold title, and description
    - Use distinct icons for each highlight (bed, size, view, wifi, etc.)
    - Conditionally render: only display if `$highlights` is not empty
    - Ensure no empty container or placeholder is visible when highlights are unavailable
    - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [x] 4. Checkpoint - Verify gallery, highlights, and typography
  - Ensure all tests pass, ask the user if questions arise.

- [x] 5. Implement amenity section with expand/collapse
  - [x] 5.1 Redesign amenity display with card-based layout and distinct icons
    - Replace generic checkmark icons with distinct SVG icons per amenity type (wifi, ac, tv, bath, etc.)
    - Wrap each amenity in a rounded card container with background fill and padding ≥8px
    - Display amenities in a responsive grid: 2 columns on `sm:` and above, single column on mobile
    - _Requirements: 3.1, 3.2, 7.3_

  - [x] 5.2 Add expand/collapse functionality with Alpine.js
    - Use Alpine.js `x-data` to manage expanded state
    - Show only first 6 amenities by default when total >6
    - Add "Tampilkan semua fasilitas" button when >6 amenities exist
    - Implement expand animation (300ms) to reveal remaining amenities
    - Toggle button label to "Sembunyikan" when expanded
    - Collapse animation (300ms) when "Sembunyikan" is clicked
    - Add `aria-expanded` attribute to the toggle button for accessibility
    - _Requirements: 3.3, 3.4, 3.5_

- [x] 6. Implement embedded location map
  - [x] 6.1 Add Leaflet.js map section to the Blade template
    - Add Leaflet.js CSS and JS via CDN using `@push('styles')` and `@push('scripts')`
    - Create map section below amenities with heading "Lokasi" and area text label
    - Initialize Leaflet map centered on hotel coordinates with zoom level 15
    - Add pin marker at hotel position with popup showing hotel name on click
    - Set container height: 300px on ≥744px viewports, 200px on <744px, with 8px border-radius
    - Implement fallback: display static placeholder with "Peta tidak tersedia" message if tiles fail to load (no interactive features — no zoom controls, no markers, no popups)
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

- [x] 7. Checkpoint - Verify amenities and map
  - Ensure all tests pass, ask the user if questions arise.

- [x] 8. Polish booking sidebar
  - [x] 8.1 Enhance booking sidebar styling and price display
    - Set price font size to at least 1.5× base body text with "per malam" label in base size
    - Add visible border and drop shadow for elevation effect on sidebar container
    - Ensure price breakdown shows nightly rate × nights line item with total separated by horizontal divider
    - Add trust indicator text ("Konfirmasi instan") below the booking button in body text size or smaller
    - Style disabled state with reduced opacity when room is unavailable or dates are invalid (negative nights, check-out before check-in, impossible date ranges)
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

  - [x] 8.2 Implement sticky sidebar behavior
    - Apply `sticky top-[24px]` positioning on viewports ≥1128px
    - Remove sticky positioning on viewports <1128px (sidebar scrolls with content)
    - Ensure sidebar stops sticking when its bottom reaches the footer boundary
    - _Requirements: 6.2, 6.5_

- [x] 9. Implement responsive mobile optimizations
  - [x] 9.1 Add mobile fixed bottom bar and responsive layout
    - Stack content and sidebar vertically on <768px viewports
    - Create fixed bottom bar (max-height 72px) on mobile showing price + "Pesan" button
    - "Pesan" button scrolls page to the full booking form section
    - Add bottom padding ≥ bottom bar height to prevent content occlusion
    - Ensure all interactive elements have minimum 44×44px touch targets on mobile
    - _Requirements: 7.1, 7.2, 7.4, 7.5_

  - [x] 9.2 Apply consistent section spacing and fade-in animations
    - Set 32px spacing between major sections on ≥768px viewports
    - Set 24px spacing between major sections on <768px viewports
    - Add staggered fade-in animation to each major section (300ms per section, 100ms delay between sections)
    - Apply `animation-delay` inline styles with incremental 100ms offsets
    - _Requirements: 6.1, 6.3_

- [x] 10. Final checkpoint - Full integration verification
  - Ensure all tests pass, ask the user if questions arise.

- [ ]* 11. Write integration tests for room detail page
  - [ ]* 11.1 Write HTTP integration tests
    - Test GET `/rooms/{id}` returns 200 and contains expected HTML elements
    - Verify breadcrumb structure with chevron separators in response
    - Verify map container is present in response HTML
    - Verify highlights section renders for known room types
    - Verify amenity expand button is present when >6 amenities
    - _Requirements: 1.1, 2.1, 3.3, 4.1, 6.4_

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- No property-based tests are included as this feature is entirely UI/rendering focused (per design document)
- Leaflet.js is loaded via CDN only on the room detail page to avoid unnecessary bundle size
- All interactivity uses Alpine.js inline directives, consistent with the existing codebase
- No database schema changes are required — highlights and map data are static controller config

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "1.2"] },
    { "id": 1, "tasks": ["2.1", "2.2", "2.3", "3.1"] },
    { "id": 2, "tasks": ["2.4", "5.1"] },
    { "id": 3, "tasks": ["5.2", "6.1"] },
    { "id": 4, "tasks": ["8.1", "8.2"] },
    { "id": 5, "tasks": ["9.1", "9.2"] },
    { "id": 6, "tasks": ["11.1"] }
  ]
}
```
