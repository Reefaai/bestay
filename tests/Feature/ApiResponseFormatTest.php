<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiResponseFormatTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that validation errors return 422 with structured JSON containing field-specific messages.
     */
    public function test_register_validation_returns_422_with_field_errors(): void
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'email',
                    'password',
                ],
            ]);
    }

    /**
     * Test that login validation errors return 422 with structured JSON.
     */
    public function test_login_validation_returns_422_with_field_errors(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email',
                    'password',
                ],
            ]);
    }

    /**
     * Test that unauthenticated requests to protected endpoints return 401 JSON.
     */
    public function test_unauthenticated_request_returns_401_json(): void
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Test that unauthenticated requests without Accept header still return 401 JSON for API routes.
     */
    public function test_unauthenticated_request_without_accept_header_returns_401_json(): void
    {
        $response = $this->get('/api/profile');

        $response->assertStatus(401);
    }

    /**
     * Test that room creation validation returns 422 with field-specific errors.
     */
    public function test_store_room_validation_returns_422_with_field_errors(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/rooms', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'type',
                    'price_per_night',
                    'capacity',
                ],
            ]);
    }

    /**
     * Test that booking creation validation returns 422 with field-specific errors.
     */
    public function test_store_booking_validation_returns_422_with_field_errors(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/bookings', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'room_id',
                    'check_in',
                    'check_out',
                ],
            ]);
    }

    /**
     * Test that admin status update validation returns 422 with field-specific errors.
     */
    public function test_update_status_validation_returns_422_with_field_errors(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a booking to update
        $user = User::factory()->create(['role' => 'user']);
        $room = \App\Models\Room::factory()->create(['is_active' => true]);
        $booking = \App\Models\Booking::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/bookings/{$booking->id}/status", []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'status',
                ],
            ]);
    }

    /**
     * Test that non-admin access to admin endpoints returns 403 JSON.
     */
    public function test_non_admin_access_returns_403_json(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/admin/bookings');

        $response->assertStatus(403)
            ->assertJson(['message' => 'Forbidden. Admin access required.']);
    }
}
