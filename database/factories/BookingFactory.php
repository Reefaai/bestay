<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('+1 day', '+30 days');
        $checkOut = fake()->dateTimeBetween($checkIn->format('Y-m-d') . ' +1 day', $checkIn->format('Y-m-d') . ' +7 days');
        $nights = (int) $checkIn->diff($checkOut)->days;

        return [
            'user_id' => User::factory(),
            'room_id' => Room::factory(),
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'total_price' => $nights * fake()->randomFloat(2, 100000, 500000),
            'status' => 'confirmed',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the booking is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the booking is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Set specific check-in and check-out dates.
     */
    public function forDates(string $checkIn, string $checkOut): static
    {
        return $this->state(fn (array $attributes) => [
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ]);
    }
}
