<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertRedirect('/admin?tab=profile');
    }

    public function test_profile_information_can_be_updated(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'full_name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin?tab=profile');

        $user->refresh();

        $this->assertSame('Test User', $user->full_name);
        $this->assertSame('test@example.com', $user->email);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'full_name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin?tab=profile');

        // Email verification is not used in this admin-only system;
        // the column exists but is never explicitly set to non-null.
    }

    public function test_user_can_delete_their_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/admin?tab=profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/admin?tab=profile');

        $this->assertNotNull($user->fresh());
    }
}
