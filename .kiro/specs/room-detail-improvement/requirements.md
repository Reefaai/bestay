# Requirements Document

## Introduction

Peningkatan halaman detail kamar (room detail page) pada aplikasi Bestay agar tampilan lebih menarik, modern, dan memberikan pengalaman pengguna yang lebih baik. Halaman saat ini sudah memiliki struktur dasar (hero image, deskripsi, fasilitas, rating, kebijakan, dan sidebar booking), namun perlu dipoles agar lebih polished dan engaging secara visual.

## Glossary

- **Room_Detail_Page**: Halaman yang menampilkan informasi lengkap tentang satu kamar, termasuk gambar, deskripsi, fasilitas, rating, kebijakan, dan form booking.
- **Image_Gallery**: Komponen yang menampilkan beberapa gambar kamar dalam format grid atau carousel yang interaktif.
- **Amenity_Section**: Bagian halaman yang menampilkan daftar fasilitas kamar dengan ikon yang sesuai.
- **Booking_Sidebar**: Panel di sisi kanan halaman yang berisi informasi harga dan form pemesanan kamar.
- **Map_Section**: Bagian halaman yang menampilkan peta lokasi hotel secara embedded menggunakan Leaflet.js dan OpenStreetMap.
- **Breadcrumb**: Navigasi hierarkis yang menunjukkan posisi halaman saat ini dalam struktur situs.
- **Room_Highlight**: Fitur unggulan kamar yang ditampilkan secara menonjol di bagian atas detail kamar.

## Requirements

### Requirement 1: Image Gallery yang Lebih Menarik

**User Story:** As a guest, I want to see multiple room images in an attractive gallery layout, so that I can better visualize the room before booking.

#### Acceptance Criteria

1. WHEN a room has an image, THE Room_Detail_Page SHALL display the hero image in a visually appealing grid layout with rounded corners (border-radius 12px) and a subtle hover shadow effect.
2. WHEN a user hovers over the hero image, THE Room_Detail_Page SHALL display a zoom-in animation scaling to 1.05× with a CSS transition duration of 300ms and overflow hidden on the container.
3. WHEN a room does not have an image, THE Room_Detail_Page SHALL display a styled placeholder with a gradient background (from gray-200 to gray-300) and a centered camera icon.
4. THE Image_Gallery SHALL maintain a 16:9 aspect ratio on viewports 768px and above, and a 4:3 aspect ratio on viewports below 768px.

### Requirement 2: Room Highlights Section

**User Story:** As a guest, I want to quickly see the key highlights of a room, so that I can understand its unique selling points at a glance.

#### Acceptance Criteria

1. WHEN the Room_Detail_Page loads, THE Room_Highlight section SHALL display exactly 3 or 4 highlights determined by the room type, where each room type (standard, deluxe, suite, family) has a fixed set of highlights defined by the system (e.g., pemandangan, ukuran kamar, fitur unggulan).
2. THE Room_Highlight section SHALL display each highlight as a row or card containing: one icon visually distinct from the other highlights' icons, a bold title of no more than 30 characters, and a description of no more than 80 characters.
3. THE Room_Highlight section SHALL appear between the room meta information section (capacity, price, bed info) and the description section ("Tentang kamar ini").
4. IF highlight data is not available for a given room type, THEN THE Room_Highlight section SHALL not be rendered, and no empty container or placeholder SHALL be visible to the user.

### Requirement 3: Improved Amenity Display

**User Story:** As a guest, I want to see room amenities displayed with distinctive icons and better visual grouping, so that I can quickly identify available facilities.

#### Acceptance Criteria

1. THE Amenity_Section SHALL display each amenity with a distinct icon that visually represents the amenity's category (e.g., WiFi icon for internet, snowflake for AC, TV icon for television), where no two different amenity types share the same icon.
2. THE Amenity_Section SHALL group amenities within a card-based layout where each amenity item is contained in a rounded container with a background fill distinguishable from the page canvas and internal padding of at least 8px.
3. WHEN there are more than 6 amenities, THE Amenity_Section SHALL display only the first 6 amenities and render a "Tampilkan semua fasilitas" button below them. IF there are 6 or fewer amenities, THEN THE Amenity_Section SHALL display all amenities without a toggle button.
4. WHEN the user clicks "Tampilkan semua fasilitas", THE Amenity_Section SHALL reveal all remaining amenities with an expand animation lasting between 200ms and 400ms, and the button label SHALL change to "Sembunyikan" to allow collapsing.
5. WHEN the user clicks "Sembunyikan", THE Amenity_Section SHALL hide the additional amenities beyond the first 6 with a collapse animation lasting between 200ms and 400ms, and the button label SHALL revert to "Tampilkan semua fasilitas".

### Requirement 4: Embedded Location Map

**User Story:** As a guest, I want to see the hotel location on a map, so that I can understand the property's surroundings and plan my trip.

#### Acceptance Criteria

1. THE Room_Detail_Page SHALL display an embedded interactive map section using Leaflet.js with OpenStreetMap tiles below the amenities section.
2. THE Map_Section SHALL display an interactive map centered on the hotel coordinates with a pin marker indicating the hotel position at a default zoom level of 15.
3. THE Map_Section SHALL include a section heading "Lokasi" and a text label of no more than 100 characters showing the hotel area name above the map.
4. THE Map_Section SHALL render the map in a container with a height of 300px on viewports 744px and above, and 200px on viewports below 744px, with border-radius of 8px.
5. WHEN a user clicks the map marker, THE Map_Section SHALL display a popup with the hotel name.
6. IF the map tiles fail to load, THEN THE Map_Section SHALL display a static placeholder container at the same dimensions with a text message indicating the map is unavailable, with no interactive features (no zoom controls, no marker clicks, no popups).

### Requirement 5: Polished Booking Sidebar

**User Story:** As a guest, I want the booking sidebar to feel premium and trustworthy, so that I feel confident making a reservation.

#### Acceptance Criteria

1. THE Booking_Sidebar SHALL display the price in a font size at least 1.5× the base body text size, followed by a "per malam" label in base body text size, establishing a two-level typographic hierarchy between price and label.
2. THE Booking_Sidebar SHALL render its container with a visible border and a drop shadow that creates a perceivable elevation difference from the page background, ensuring the sidebar boundary is distinguishable when placed adjacent to white content.
3. WHEN the user selects valid dates and the room is available, THE Booking_Sidebar SHALL display a price breakdown containing: a line item showing the nightly rate multiplied by the number of nights, and a total row separated from the line items by a visible horizontal divider.
4. THE Booking_Sidebar SHALL display a trust indicator text (e.g., "Konfirmasi instan" or "Pembatalan gratis") positioned below the booking button, rendered in a font size no larger than the body text size.
5. IF the room is not available for the selected dates OR the user selects invalid dates (e.g., negative nights, check-out before check-in, or impossible date ranges), THEN THE Booking_Sidebar SHALL hide the price breakdown and the booking button SHALL be displayed in a disabled state with reduced opacity.

### Requirement 6: Visual Polish and Micro-interactions

**User Story:** As a guest, I want the page to feel smooth and responsive with subtle animations, so that the browsing experience feels premium.

#### Acceptance Criteria

1. WHEN the Room_Detail_Page loads, THE page content SHALL fade in with a staggered animation where each major section fades in over 300ms, appearing sequentially with a 100ms delay between each section.
2. WHILE the viewport width is 1128px or greater, THE Booking_Sidebar SHALL remain sticky with a top offset of 24px from the viewport top, staying visible as the user scrolls until the sidebar bottom reaches the footer boundary.
3. THE Room_Detail_Page SHALL use consistent spacing of 32px between major sections when the viewport width is 768px or greater, and 24px when the viewport width is less than 768px.
4. THE Breadcrumb navigation SHALL use chevron (›) separators instead of slash characters and SHALL display a text color change on hover for clickable items with a transition duration of 200ms.
5. WHILE the viewport width is less than 1128px, THE Booking_Sidebar SHALL scroll with the page content without sticky positioning.

### Requirement 7: Responsive Design Improvements

**User Story:** As a guest using a mobile device, I want the room detail page to be well-optimized for smaller screens, so that I can browse comfortably on my phone.

#### Acceptance Criteria

1. WHILE the viewport width is less than 768px, THE Room_Detail_Page SHALL stack the content and booking sidebar vertically with the booking section appearing after the room information.
2. WHILE the viewport width is less than 768px, THE Booking_Sidebar SHALL display as a fixed bottom bar with a maximum height of 72px showing the per-night price and a "Pesan" button that scrolls the page to the full booking form section.
3. WHILE the viewport width is less than 768px, THE Amenity_Section SHALL display amenities in a single column layout.
4. WHILE the viewport width is less than 768px, THE Room_Detail_Page SHALL ensure all interactive elements (buttons, links, date inputs) have a minimum touch target size of 44x44 pixels.
5. WHILE the viewport width is less than 768px, THE Room_Detail_Page SHALL apply bottom padding equal to or greater than the fixed bottom bar height so that no content is obscured by the fixed bottom bar.

### Requirement 8: Improved Typography and Content Hierarchy

**User Story:** As a guest, I want the text content to be easy to read with clear visual hierarchy, so that I can quickly find the information I need.

#### Acceptance Criteria

1. WHILE the viewport width is 744px or greater, THE Room_Detail_Page SHALL display the room name heading at a font size of 28px, and WHILE the viewport width is below 744px, THE Room_Detail_Page SHALL display the room name heading at a font size of 22px.
2. THE Room_Detail_Page SHALL use a line height of 1.6 for the room description paragraph text.
3. THE Room_Detail_Page SHALL style all section headings (room description heading, amenities heading, reviews heading, and policies heading) with a font size of 20px, semi-bold weight (600), and 24px bottom margin.
4. THE Room_Detail_Page SHALL display the room type badge with a visually distinct colored background for each room type: gold for suite, blue for deluxe, green for family, and gray for standard. THE system SHALL enforce that badge colors match their corresponding room types, preventing mismatches (e.g., a gold badge on a standard room).
