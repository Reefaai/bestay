<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement([
            'booking_confirmed',
            'booking_cancelled',
            'status_updated',
        ]);

        return [
            'user_id' => User::factory(),
            'booking_id' => Booking::factory(),
            'type' => $type,
            'title' => $this->titleForType($type),
            'message' => fake()->sentence(),
            'is_read' => false,
            'read_at' => null,
        ];
    }

    /**
     * Indicate that the notification has been read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Generate a title based on notification type.
     */
    private function titleForType(string $type): string
    {
        return match ($type) {
            'booking_confirmed' => 'Booking Dikonfirmasi',
            'booking_cancelled' => 'Booking Dibatalkan',
            'status_updated' => 'Status Booking Diperbarui',
            default => 'Notifikasi',
        };
    }
}
