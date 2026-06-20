<?php

namespace Tests\Feature\Auth;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Livewire\Pages\Auth\ConfirmPassword;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A2a witness — confirm-password flow.
 *
 * NOTE: the success path calls route('dashboard'), which does NOT exist (only
 * admin.dashboard). Until the dashboard-route bug is fixed, the success test
 * throws RouteNotFoundException — that failure is expected and confirms the bug.
 * Redirect target is asserted generically so the test stays valid after the fix.
 */
class ConfirmPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_is_confirmed_with_correct_password(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT]); // factory password = 'password'
        $this->actingAs($user);

        Livewire::test(ConfirmPassword::class)
            ->set('password', 'password')
            ->call('confirmPassword')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertNotNull(session('auth.password_confirmed_at'));
    }

    public function test_error_with_incorrect_password(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT]);
        $this->actingAs($user);

        Livewire::test(ConfirmPassword::class)
            ->set('password', 'wrong-password')
            ->call('confirmPassword')
            ->assertHasErrors(['password']);
    }
}
