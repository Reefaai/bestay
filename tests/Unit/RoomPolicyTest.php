<?php

namespace Tests\Unit;

use App\Models\Room;
use App\Models\User;
use App\Policies\RoomPolicy;
use PHPUnit\Framework\TestCase;

class RoomPolicyTest extends TestCase
{
    private RoomPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new RoomPolicy();
    }

    private function makeUser(string $role = 'user'): User
    {
        $user = new User();
        $user->role = $role;

        return $user;
    }

    private function makeRoom(): Room
    {
        return new Room();
    }

    public function test_viewAny_allows_authenticated_user(): void
    {
        $user = $this->makeUser('user');
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_viewAny_allows_admin(): void
    {
        $admin = $this->makeUser('admin');
        $this->assertTrue($this->policy->viewAny($admin));
    }

    public function test_viewAny_allows_guest(): void
    {
        $this->assertTrue($this->policy->viewAny(null));
    }

    public function test_view_allows_authenticated_user(): void
    {
        $user = $this->makeUser('user');
        $room = $this->makeRoom();
        $this->assertTrue($this->policy->view($user, $room));
    }

    public function test_view_allows_admin(): void
    {
        $admin = $this->makeUser('admin');
        $room = $this->makeRoom();
        $this->assertTrue($this->policy->view($admin, $room));
    }

    public function test_view_allows_guest(): void
    {
        $room = $this->makeRoom();
        $this->assertTrue($this->policy->view(null, $room));
    }

    public function test_create_allows_admin(): void
    {
        $admin = $this->makeUser('admin');
        $this->assertTrue($this->policy->create($admin));
    }

    public function test_create_denies_regular_user(): void
    {
        $user = $this->makeUser('user');
        $this->assertFalse($this->policy->create($user));
    }

    public function test_update_allows_admin(): void
    {
        $admin = $this->makeUser('admin');
        $room = $this->makeRoom();
        $this->assertTrue($this->policy->update($admin, $room));
    }

    public function test_update_denies_regular_user(): void
    {
        $user = $this->makeUser('user');
        $room = $this->makeRoom();
        $this->assertFalse($this->policy->update($user, $room));
    }

    public function test_delete_allows_admin(): void
    {
        $admin = $this->makeUser('admin');
        $room = $this->makeRoom();
        $this->assertTrue($this->policy->delete($admin, $room));
    }

    public function test_delete_denies_regular_user(): void
    {
        $user = $this->makeUser('user');
        $room = $this->makeRoom();
        $this->assertFalse($this->policy->delete($user, $room));
    }
}
