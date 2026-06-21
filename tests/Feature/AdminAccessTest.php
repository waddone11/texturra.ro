<?php

namespace Tests\Feature;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Since the Filament swap, /admin is the Filament panel, gated by FilamentUser::
 * canAccessPanel (type in {ADMIN, EMPLOYEE}). These tests pin that guard.
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

        // Filament forbids non-panel users (canAccessPanel = false).
        $this->actingAs($client)->get('/admin')->assertForbidden();
    }

    public function test_guest_is_redirected_to_filament_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }
}
