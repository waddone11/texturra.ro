<?php

namespace Tests\Feature\Profile;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Livewire\Profile\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A2a witness — profile information update flow.
 */
class UpdateProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_and_email_are_updated(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT]);
        $this->actingAs($user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('name', 'New Name')
            ->set('email', 'new-email@example.com')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $fresh = $user->fresh();
        $this->assertSame('New Name', $fresh->name);
        $this->assertSame('new-email@example.com', $fresh->email);
    }

    public function test_changing_email_resets_verification(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT]); // verified by factory
        $this->assertNotNull($user->email_verified_at);
        $this->actingAs($user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('name', $user->name)
            ->set('email', 'changed@example.com')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_unchanged_email_keeps_verification(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT]);
        $this->actingAs($user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('name', 'Renamed')
            ->set('email', $user->email)
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
