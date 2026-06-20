<?php

namespace Tests\Unit;

use App\Enums\UserType;
use App\Models\User;
use Tests\TestCase;

/**
 * Pure unit test (no DB) for the role helpers used by the admin guard.
 */
class UserRoleTest extends TestCase
{
    public function test_is_admin_reflects_type(): void
    {
        $user = new User();
        $user->type = UserType::ADMIN;

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isClient());
        $this->assertTrue($user->isRole('admin'));
    }

    public function test_is_client_reflects_type(): void
    {
        $user = new User();
        $user->type = UserType::CLIENT;

        $this->assertTrue($user->isClient());
        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isRole('client'));
    }
}
