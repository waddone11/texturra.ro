<?php

namespace Tests\Feature\Account;

use App\Enums\UserType;
use App\Livewire\Account\ChangePassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'type'     => UserType::CLIENT,
            'password' => Hash::make('oldpassword'),
        ]);
        $this->actingAs($user);

        Livewire::test(ChangePassword::class)
            ->set('current_password', 'oldpassword')
            ->set('new_password', 'newpassword1')
            ->set('new_password_confirmation', 'newpassword1')
            ->call('updatePassword');

        $this->assertTrue(Hash::check('newpassword1', $user->fresh()->password));
    }

    /**
     * Faza 4 Grup B regression: ChangePassword must stop writing the plaintext
     * users.raw_password column (PII, audit S5). SKIPPED until that line is removed.
     */
    public function test_change_password_does_not_store_plaintext_raw_password(): void
    {
        $user = User::factory()->create([
            'type'     => UserType::CLIENT,
            'password' => Hash::make('oldpassword'),
        ]);
        $this->actingAs($user);

        Livewire::test(ChangePassword::class)
            ->set('current_password', 'oldpassword')
            ->set('new_password', 'newpassword1')
            ->set('new_password_confirmation', 'newpassword1')
            ->call('updatePassword');

        $this->assertNull($user->fresh()->raw_password);
    }
}
