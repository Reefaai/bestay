<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Kamar ' . fake()->unique()->numberBetween(100, 999),
            'type' => fake()->randomElement(['standard', 'deluxe', 'suite']),
            'description' => fake()->sentence(),
            'price_per_night' => fake()->randomFloat(2, 100000, 1000000),
            'capacity' => fake()->numberBetween(1, 6),
            'image_url' => fake()->optional()->imageUrl(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the room is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set the room type to standard.
     */
    public function standard(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'standard',
        ]);
    }

    /**
     * Set the room type to deluxe.
     */
    public function deluxe(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'deluxe',
        ]);
    }

    /**
     * Set the room type to suite.
     */
    public function suite(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'suite',
        ]);
    }
}
