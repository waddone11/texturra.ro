<?php

namespace Tests\Feature\Profile;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Livewire\Profile\UpdatePasswordForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A2a witness — Breeze profile update-password-form Volt component.
 * (Distinct from App\Livewire\Account\ChangePassword, covered by ChangePasswordTest.)
 */
class UpdatePasswordFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_is_updated_with_correct_current_password(): void
    {
        $user = User::factory()->create([
            'type'     => UserType::CLIENT,
            'password' => Hash::make('oldpassword'),
        ]);
        $this->actingAs($user);

        Livewire::test(UpdatePasswordForm::class)
            ->set('current_password', 'oldpassword')
            ->set('password', 'newpassword1')
            ->set('password_confirmation', 'newpassword1')
            ->call('updatePassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('newpassword1', $user->fresh()->password));
    }

    public function test_error_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'type'     => UserType::CLIENT,
            'password' => Hash::make('oldpassword'),
        ]);
        $this->actingAs($user);

        Livewire::test(UpdatePasswordForm::class)
            ->set('current_password', 'wrong')
            ->set('password', 'newpassword1')
            ->set('password_confirmation', 'newpassword1')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);
    }
}
