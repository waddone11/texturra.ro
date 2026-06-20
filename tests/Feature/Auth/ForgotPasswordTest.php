<?php

namespace Tests\Feature\Auth;

use App\Enums\UserType;
use App\Livewire\Pages\Auth\ForgotPassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A2a witness — forgot-password flow. Pins behaviour on the Volt component so the
 * conversion to class-based Livewire can be proven equivalent.
 */
class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_reset_link_is_sent_for_valid_email(): void
    {
        Notification::fake();
        $user = User::factory()->create(['type' => UserType::CLIENT]);

        Livewire::test(ForgotPassword::class)
            ->set('email', $user->email)
            ->call('sendPasswordResetLink')
            ->assertHasNoErrors();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_validation_error_for_invalid_email(): void
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'not-an-email')
            ->call('sendPasswordResetLink')
            ->assertHasErrors(['email']);
    }
}
