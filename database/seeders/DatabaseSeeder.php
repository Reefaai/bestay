<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentStatusLog;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin Bestay',
            'email' => 'admin@bestay.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        // Create regular users
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'user@bestay.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@bestay.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        // Create Standard rooms
        $standard1 = Room::create([
            'name' => 'Kamar Melati 101',
            'type' => 'standard',
            'description' => 'Kamar standar yang nyaman dengan pemandangan taman. Dilengkapi AC, TV LED 32 inch, WiFi gratis, dan kamar mandi dalam dengan air panas. Cocok untuk perjalanan bisnis atau liburan singkat.',
            'price_per_night' => 350000.00,
            'capacity' => 2,
            'image_url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&h=400&fit=crop&q=80',
            'is_active' => true,
        ]);

        $standard2 = Room::create([
            'name' => 'Kamar Melati 102',
            'type' => 'standard',
            'description' => 'Kamar standar dengan desain modern minimalis. Fasilitas lengkap termasuk meja kerja, brankas, dan minibar. Lokasi strategis dekat lobby.',
            'price_per_night' => 375000.00,
            'capacity' => 2,
            'image_url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=600&h=400&fit=crop&q=80',
            'is_active' => true,
        ]);

        $standard3 = Room::create([
            'name' => 'Kamar Melati 103',
            'type' => 'standard',
            'description' => 'Kamar standar di lantai atas dengan pemandangan kota. Tenang dan nyaman untuk istirahat setelah hari yang panjang.',
            'price_per_night' => 400000.00,
            'capacity' => 2,
            'image_url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600&h=400&fit=crop&q=80',
            'is_active' => true,
        ]);

        // Create Deluxe rooms
        $deluxe1 = Room::create([
            'name' => 'Kamar Anggrek 201',
            'type' => 'deluxe',
            'description' => 'Kamar deluxe luas dengan balkon pribadi menghadap kolam renang. King-size bed, sofa, TV 43 inch, dan bathtub. Termasuk sarapan untuk 2 orang.',
            'price_per_night' => 650000.00,
            'capacity' => 3,
            'image_url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600&h=400&fit=crop&q=80',
            'is_active' => true,
        ]);

        $deluxe2 = Room::create([
            'name' => 'Kamar Anggrek 202',
            'type' => 'deluxe',
            'description' => 'Kamar deluxe premium dengan interior kayu tropis. Dilengkapi rain shower, amenities premium, dan akses langsung ke area spa.',
            'price_per_night' => 700000.00,
            'capacity' => 3,
            'image_url' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=600&h=400&fit=crop&q=80',
            'is_active' => true,
        ]);

        // Create Suite rooms
        $suite1 = Room::create([
            'name' => 'Suite Cendana 301',
            'type' => 'suite',
            'description' => 'Suite mewah dengan ruang tamu terpisah, dapur kecil, dan jacuzzi pribadi. Pemandangan panorama 180 derajat. Butler service 24 jam.',
            'price_per_night' => 1500000.00,
            'capacity' => 4,
            'image_url' => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?w=600&h=400&fit=crop&q=80',
            'is_active' => true,
        ]);

        $suite2 = Room::create([
            'name' => 'Suite Cendana 302',
            'type' => 'suite',
            'description' => 'Presidential suite dengan 2 kamar tidur, ruang makan, dan teras rooftop privat. Pengalaman menginap paling eksklusif di Bestay.',
            'price_per_night' => 2500000.00,
            'capacity' => 4,
            'image_url' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=600&h=400&fit=crop&q=80',
            'is_active' => true,
        ]);

        // Create Family rooms
        $family1 = Room::create([
            'name' => 'Kamar Keluarga 401',
            'type' => 'family',
            'description' => 'Kamar keluarga luas dengan 2 queen-size bed, area bermain anak, dan connecting door. Ideal untuk liburan keluarga dengan anak-anak.',
            'price_per_night' => 850000.00,
            'capacity' => 5,
            'image_url' => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=600&h=400&fit=crop&q=80',
            'is_active' => true,
        ]);

        $family2 = Room::create([
            'name' => 'Kamar Keluarga 402',
            'type' => 'family',
            'description' => 'Kamar keluarga premium dengan bunk bed untuk anak, TV dengan channel kartun, dan akses langsung ke kids club. Sarapan gratis untuk seluruh keluarga.',
            'price_per_night' => 950000.00,
            'capacity' => 6,
            'image_url' => 'https://images.unsplash.com/photo-1540518614846-7eded433c457?w=600&h=400&fit=crop&q=80',
            'is_active' => true,
        ]);

        // Create 1 inactive room
        Room::create([
            'name' => 'Kamar Renovasi 501',
            'type' => 'standard',
            'description' => 'Sedang dalam renovasi.',
            'price_per_night' => 300000.00,
            'capacity' => 2,
            'image_url' => null,
            'is_active' => false,
        ]);

        // Create bookings for user 1
        $checkIn1 = Carbon::today()->addDays(3)->format('Y-m-d');
        $checkOut1 = Carbon::today()->addDays(6)->format('Y-m-d');

        $booking1 = Booking::create([
            'user_id' => $user->id,
            'room_id' => $standard1->id,
            'check_in' => $checkIn1,
            'check_out' => $checkOut1,
            'total_price' => 3 * $standard1->price_per_night,
            'status' => 'confirmed',
            'notes' => 'Minta kamar lantai bawah, dekat lift',
        ]);

        $checkIn2 = Carbon::today()->addDays(10)->format('Y-m-d');
        $checkOut2 = Carbon::today()->addDays(13)->format('Y-m-d');

        $booking2 = Booking::create([
            'user_id' => $user->id,
            'room_id' => $deluxe1->id,
            'check_in' => $checkIn2,
            'check_out' => $checkOut2,
            'total_price' => 3 * $deluxe1->price_per_night,
            'status' => 'pending',
            'notes' => 'Butuh extra bed untuk anak',
        ]);

        $checkIn3 = Carbon::today()->addDays(20)->format('Y-m-d');
        $checkOut3 = Carbon::today()->addDays(23)->format('Y-m-d');

        $booking3 = Booking::create([
            'user_id' => $user->id,
            'room_id' => $suite1->id,
            'check_in' => $checkIn3,
            'check_out' => $checkOut3,
            'total_price' => 3 * $suite1->price_per_night,
            'status' => 'confirmed',
            'notes' => null,
        ]);

        // Create bookings for user 2
        $checkIn4 = Carbon::today()->addDays(5)->format('Y-m-d');
        $checkOut4 = Carbon::today()->addDays(7)->format('Y-m-d');

        $booking4 = Booking::create([
            'user_id' => $user2->id,
            'room_id' => $family1->id,
            'check_in' => $checkIn4,
            'check_out' => $checkOut4,
            'total_price' => 2 * $family1->price_per_night,
            'status' => 'confirmed',
            'notes' => 'Liburan keluarga, ada 2 anak kecil',
        ]);

        $checkIn5 = Carbon::today()->subDays(5)->format('Y-m-d');
        $checkOut5 = Carbon::today()->subDays(2)->format('Y-m-d');

        $booking5 = Booking::create([
            'user_id' => $user2->id,
            'room_id' => $deluxe2->id,
            'check_in' => $checkIn5,
            'check_out' => $checkOut5,
            'total_price' => 3 * $deluxe2->price_per_night,
            'status' => 'completed',
            'notes' => null,
        ]);

        // A cancelled booking
        $booking6 = Booking::create([
            'user_id' => $user->id,
            'room_id' => $standard2->id,
            'check_in' => Carbon::today()->addDays(7)->format('Y-m-d'),
            'check_out' => Carbon::today()->addDays(9)->format('Y-m-d'),
            'total_price' => 2 * $standard2->price_per_night,
            'status' => 'cancelled',
            'notes' => 'Batal karena perubahan jadwal',
        ]);

        // Create notifications
        Notification::create([
            'user_id' => $user->id,
            'booking_id' => $booking1->id,
            'type' => 'booking_confirmed',
            'title' => 'Booking Dikonfirmasi',
            'message' => "Booking Anda untuk {$standard1->name} ({$checkIn1} - {$checkOut1}) telah dikonfirmasi.",
            'is_read' => true,
            'read_at' => now()->subHours(2),
        ]);

        Notification::create([
            'user_id' => $user->id,
            'booking_id' => $booking2->id,
            'type' => 'status_updated',
            'title' => 'Status Booking Diperbarui',
            'message' => "Booking Anda untuk {$deluxe1->name} sedang menunggu konfirmasi.",
            'is_read' => false,
            'read_at' => null,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'booking_id' => $booking3->id,
            'type' => 'booking_confirmed',
            'title' => 'Booking Dikonfirmasi',
            'message' => "Booking Anda untuk {$suite1->name} ({$checkIn3} - {$checkOut3}) telah dikonfirmasi.",
            'is_read' => false,
            'read_at' => null,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'booking_id' => $booking6->id,
            'type' => 'booking_cancelled',
            'title' => 'Booking Dibatalkan',
            'message' => "Booking Anda untuk {$standard2->name} telah dibatalkan.",
            'is_read' => true,
            'read_at' => now()->subDay(),
        ]);

        Notification::create([
            'user_id' => $user2->id,
            'booking_id' => $booking4->id,
            'type' => 'booking_confirmed',
            'title' => 'Booking Dikonfirmasi',
            'message' => "Booking Anda untuk {$family1->name} ({$checkIn4} - {$checkOut4}) telah dikonfirmasi.",
            'is_read' => false,
            'read_at' => null,
        ]);

        // =============================================
        // PAYMENT DATA
        // =============================================

        // Payment 1: PAID — for booking1 (confirmed, user Budi, standard1)
        $payment1 = Payment::create([
            'booking_id' => $booking1->id,
            'reference' => 'PAY-' . Carbon::today()->format('Ymd') . '-ABC123',
            'amount' => $booking1->total_price,
            'method' => 'bank_transfer',
            'status' => 'paid',
            'paid_at' => now()->subDays(2),
            'expires_at' => now()->subDays(2)->addMinutes(60),
            'failure_reason' => null,
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment1->id,
            'from_status' => null,
            'to_status' => 'pending',
            'actor_user_id' => $user->id,
            'actor_type' => 'guest',
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment1->id,
            'from_status' => 'pending',
            'to_status' => 'paid',
            'actor_user_id' => $user->id,
            'actor_type' => 'guest',
        ]);

        // Payment 2: PENDING — for booking2 (pending, user Budi, deluxe1)
        $payment2 = Payment::create([
            'booking_id' => $booking2->id,
            'reference' => 'PAY-' . Carbon::today()->format('Ymd') . '-DEF456',
            'amount' => $booking2->total_price,
            'method' => 'e_wallet',
            'status' => 'pending',
            'expires_at' => now()->addMinutes(45), // masih aktif, 45 menit lagi
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment2->id,
            'from_status' => null,
            'to_status' => 'pending',
            'actor_user_id' => $user->id,
            'actor_type' => 'guest',
        ]);

        // Payment 3: PAID — for booking3 (confirmed, user Budi, suite1)
        $payment3 = Payment::create([
            'booking_id' => $booking3->id,
            'reference' => 'PAY-' . Carbon::today()->format('Ymd') . '-GHI789',
            'amount' => $booking3->total_price,
            'method' => 'credit_card',
            'status' => 'paid',
            'paid_at' => now()->subDays(1),
            'expires_at' => now()->subDays(1)->addMinutes(60),
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment3->id,
            'from_status' => null,
            'to_status' => 'pending',
            'actor_user_id' => $user->id,
            'actor_type' => 'guest',
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment3->id,
            'from_status' => 'pending',
            'to_status' => 'paid',
            'actor_user_id' => $user->id,
            'actor_type' => 'guest',
        ]);

        // Payment 4: PAID — for booking4 (confirmed, user Siti, family1)
        $payment4 = Payment::create([
            'booking_id' => $booking4->id,
            'reference' => 'PAY-' . Carbon::today()->format('Ymd') . '-JKL012',
            'amount' => $booking4->total_price,
            'method' => 'bank_transfer',
            'status' => 'paid',
            'paid_at' => now()->subDays(3),
            'expires_at' => now()->subDays(3)->addMinutes(60),
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment4->id,
            'from_status' => null,
            'to_status' => 'pending',
            'actor_user_id' => $user2->id,
            'actor_type' => 'guest',
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment4->id,
            'from_status' => 'pending',
            'to_status' => 'paid',
            'actor_user_id' => $user2->id,
            'actor_type' => 'guest',
        ]);

        // Payment 5: EXPIRED — for booking6 (cancelled, user Budi, standard2)
        // Simulasi: payment dibuat lalu expired karena tidak dibayar
        $payment5 = Payment::create([
            'booking_id' => $booking6->id,
            'reference' => 'PAY-' . Carbon::today()->subDays(1)->format('Ymd') . '-MNO345',
            'amount' => $booking6->total_price,
            'method' => null,
            'status' => 'expired',
            'expires_at' => now()->subHours(23),
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment5->id,
            'from_status' => null,
            'to_status' => 'pending',
            'actor_user_id' => $user->id,
            'actor_type' => 'guest',
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment5->id,
            'from_status' => 'pending',
            'to_status' => 'expired',
            'actor_user_id' => null,
            'actor_type' => 'system',
            'reason' => 'Booking cancelled',
        ]);

        // Payment 6: FAILED — contoh payment gagal untuk booking5 (completed, user Siti)
        // Ini adalah attempt pertama yang gagal, lalu ada attempt kedua yang berhasil
        $payment6 = Payment::create([
            'booking_id' => $booking5->id,
            'reference' => 'PAY-' . Carbon::today()->subDays(6)->format('Ymd') . '-PQR678',
            'amount' => $booking5->total_price,
            'method' => 'e_wallet',
            'status' => 'failed',
            'failure_reason' => 'Saldo e-wallet tidak mencukupi',
            'expires_at' => now()->subDays(6)->addMinutes(60),
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment6->id,
            'from_status' => null,
            'to_status' => 'pending',
            'actor_user_id' => $user2->id,
            'actor_type' => 'guest',
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment6->id,
            'from_status' => 'pending',
            'to_status' => 'failed',
            'actor_user_id' => $user2->id,
            'actor_type' => 'guest',
            'reason' => 'Saldo e-wallet tidak mencukupi',
        ]);

        // Payment 7: PAID — attempt kedua untuk booking5 yang berhasil
        $payment7 = Payment::create([
            'booking_id' => $booking5->id,
            'reference' => 'PAY-' . Carbon::today()->subDays(6)->format('Ymd') . '-STU901',
            'amount' => $booking5->total_price,
            'method' => 'credit_card',
            'status' => 'paid',
            'paid_at' => now()->subDays(6)->addMinutes(15),
            'expires_at' => now()->subDays(6)->addMinutes(60),
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment7->id,
            'from_status' => null,
            'to_status' => 'pending',
            'actor_user_id' => $user2->id,
            'actor_type' => 'guest',
        ]);

        PaymentStatusLog::create([
            'payment_id' => $payment7->id,
            'from_status' => 'pending',
            'to_status' => 'paid',
            'actor_user_id' => $user2->id,
            'actor_type' => 'guest',
        ]);

        // Payment notifications
        Notification::create([
            'user_id' => $user->id,
            'booking_id' => $booking1->id,
            'type' => 'payment_succeeded',
            'title' => 'Pembayaran Berhasil',
            'message' => "Pembayaran untuk booking #{$booking1->id} berhasil dengan referensi {$payment1->reference}.",
            'is_read' => true,
            'read_at' => now()->subDays(2),
        ]);

        Notification::create([
            'user_id' => $user->id,
            'booking_id' => $booking6->id,
            'type' => 'payment_expired',
            'title' => 'Pembayaran Kedaluwarsa',
            'message' => "Pembayaran untuk booking #{$booking6->id} telah kedaluwarsa. Status: expired.",
            'is_read' => true,
            'read_at' => now()->subHours(20),
        ]);

        Notification::create([
            'user_id' => $user2->id,
            'booking_id' => $booking5->id,
            'type' => 'payment_failed',
            'title' => 'Pembayaran Gagal',
            'message' => "Pembayaran untuk booking #{$booking5->id} gagal. Status: failed.",
            'is_read' => true,
            'read_at' => now()->subDays(6),
        ]);

        Notification::create([
            'user_id' => $user2->id,
            'booking_id' => $booking5->id,
            'type' => 'payment_succeeded',
            'title' => 'Pembayaran Berhasil',
            'message' => "Pembayaran untuk booking #{$booking5->id} berhasil dengan referensi {$payment7->reference}.",
            'is_read' => true,
            'read_at' => now()->subDays(6),
        ]);
    }
}
