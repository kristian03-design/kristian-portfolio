<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Stage 1 redirects to OTP verify screen
        $response->assertRedirect(route('otp.verify'));
        $this->assertGuest();

        // Retrieve generated OTP directly from DB (otp_code is in $hidden)
        $otp = DB::table('adminlist')->where('id', $user->id)->value('otp_code');
        $this->assertNotNull($otp);

        // Submit OTP — carry the session-stored otp_user_id across test requests
        $otpResponse = $this
            ->withSession(['otp_user_id' => $user->id])
            ->post('/otp/verify', ['otp' => $otp]);

        $this->assertAuthenticatedAs($user);
        $otpResponse->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
