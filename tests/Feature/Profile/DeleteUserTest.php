<?php

namespace Tests\Feature\Profile;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

/**
 * A2a witness — delete-user-form flow.
 */
class DeleteUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_is_deleted_with_correct_password(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT]); // factory password = 'password'
        $this->actingAs($user);

        Volt::test('profile.delete-user-form')
            ->set('password', 'password')
            ->call('deleteUser')
            ->assertRedirect('/');

        $this->assertModelMissing($user);
        $this->assertGuest();
    }

    public function test_error_with_wrong_password(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT]);
        $this->actingAs($user);

        Volt::test('profile.delete-user-form')
            ->set('password', 'wrong-password')
            ->call('deleteUser')
            ->assertHasErrors(['password']);

        $this->assertNotNull($user->fresh());
    }
}
