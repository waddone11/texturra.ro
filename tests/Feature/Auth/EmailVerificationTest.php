<?php

namespace Tests\Feature\Auth;

use App\Enums\UserType;
use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;
use Tests\TestCase;

/**
 * A2a witness — verify-email flow (unverified path; the already-verified branch
 * redirects and is covered by the dashboard-route fix).
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_notification_is_sent(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create(['type' => UserType::CLIENT]);
        $this->actingAs($user);

        Volt::test('pages.auth.verify-email')
            ->call('sendVerification');

        Notification::assertSentTo($user, CustomVerifyEmail::class);
    }

    public function test_user_can_logout_from_verify_screen(): void
    {
        $user = User::factory()->unverified()->create(['type' => UserType::CLIENT]);
        $this->actingAs($user);

        Volt::test('pages.auth.verify-email')
            ->call('logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
