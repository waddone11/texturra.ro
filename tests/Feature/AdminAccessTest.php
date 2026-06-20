<?php

namespace Tests\Feature;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin access is gated by auth + CheckRole:admin,employee on the /admin group.
 * These tests pin that guard so the cleanup in Faza 4 cannot silently break it.
 */
class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_reach_admin_dashboard(): void
    {
        $admin = User::factory()->create(['type' => UserType::ADMIN]);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_client_user_is_denied_admin(): void
    {
        $client = User::factory()->create(['type' => UserType::CLIENT]);

        $this->actingAs($client)->get('/admin')->assertRedirect(route('home'));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }
}
