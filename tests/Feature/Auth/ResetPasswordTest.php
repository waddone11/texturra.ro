<?php

namespace Tests\Feature\Auth;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Livewire\Pages\Auth\ResetPassword;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A2a witness — reset-password flow.
 */
class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_is_reset_with_valid_token(): void
    {
        $user  = User::factory()->create(['type' => UserType::CLIENT]);
        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'newpassword1')
            ->set('password_confirmation', 'newpassword1')
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('newpassword1', $user->fresh()->password));
    }

    public function test_password_must_be_confirmed(): void
    {
        $user  = User::factory()->create(['type' => UserType::CLIENT]);
        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'newpassword1')
            ->set('password_confirmation', 'mismatch')
            ->call('resetPassword')
            ->assertHasErrors(['password']);
    }
}
