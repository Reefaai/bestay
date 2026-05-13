<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\User;
use App\Policies\BookingPolicy;
use PHPUnit\Framework\TestCase;

class BookingPolicyTest extends TestCase
{
    private BookingPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new BookingPolicy();
    }

    private function makeUser(string $role = 'user', int $id = 1): User
    {
        $user = new User();
        $user->role = $role;
        $user->id = $id;

        return $user;
    }

    private function makeBooking(int $userId = 1, string $status = 'confirmed'): Booking
    {
        $booking = new Booking();
        $booking->user_id = $userId;
        $booking->status = $status;

        return $booking;
    }

    public function test_viewAny_allows_any_authenticated_user(): void
    {
        $user = $this->makeUser('user');
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_viewAny_allows_admin(): void
    {
        $admin = $this->makeUser('admin');
        $this->assertTrue($this->policy->viewAny($admin));
    }

    public function test_view_allows_user_to_view_own_booking(): void
    {
        $user = $this->makeUser('user', 1);
        $booking = $this->makeBooking(1);
        $this->assertTrue($this->policy->view($user, $booking));
    }

    public function test_view_denies_user_from_viewing_others_booking(): void
    {
        $user = $this->makeUser('user', 1);
        $booking = $this->makeBooking(2);
        $this->assertFalse($this->policy->view($user, $booking));
    }

    public function test_view_allows_admin_to_view_any_booking(): void
    {
        $admin = $this->makeUser('admin', 1);
        $booking = $this->makeBooking(2);
        $this->assertTrue($this->policy->view($admin, $booking));
    }

    public function test_create_allows_any_authenticated_user(): void
    {
        $user = $this->makeUser('user');
        $this->assertTrue($this->policy->create($user));
    }

    public function test_create_allows_admin(): void
    {
        $admin = $this->makeUser('admin');
        $this->assertTrue($this->policy->create($admin));
    }

    public function test_cancel_allows_user_to_cancel_own_pending_booking(): void
    {
        $user = $this->makeUser('user', 1);
        $booking = $this->makeBooking(1, 'pending');
        $this->assertTrue($this->policy->cancel($user, $booking));
    }

    public function test_cancel_allows_user_to_cancel_own_confirmed_booking(): void
    {
        $user = $this->makeUser('user', 1);
        $booking = $this->makeBooking(1, 'confirmed');
        $this->assertTrue($this->policy->cancel($user, $booking));
    }

    public function test_cancel_denies_user_from_cancelling_own_cancelled_booking(): void
    {
        $user = $this->makeUser('user', 1);
        $booking = $this->makeBooking(1, 'cancelled');
        $this->assertFalse($this->policy->cancel($user, $booking));
    }

    public function test_cancel_denies_user_from_cancelling_own_completed_booking(): void
    {
        $user = $this->makeUser('user', 1);
        $booking = $this->makeBooking(1, 'completed');
        $this->assertFalse($this->policy->cancel($user, $booking));
    }

    public function test_cancel_denies_user_from_cancelling_others_booking(): void
    {
        $user = $this->makeUser('user', 1);
        $booking = $this->makeBooking(2, 'pending');
        $this->assertFalse($this->policy->cancel($user, $booking));
    }

    public function test_cancel_allows_admin_to_cancel_any_booking(): void
    {
        $admin = $this->makeUser('admin', 1);
        $booking = $this->makeBooking(2, 'confirmed');
        $this->assertTrue($this->policy->cancel($admin, $booking));
    }

    public function test_cancel_allows_admin_to_cancel_even_completed_booking(): void
    {
        $admin = $this->makeUser('admin', 1);
        $booking = $this->makeBooking(2, 'completed');
        $this->assertTrue($this->policy->cancel($admin, $booking));
    }
}
