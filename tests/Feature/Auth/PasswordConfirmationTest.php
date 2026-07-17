<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $this->markTestSkipped(
            'Password confirmation screen is not part of this admin-only OTP system. '
            . 'No auth.confirm-password view exists.'
        );
    }

    public function test_password_can_be_confirmed(): void
    {
        $this->markTestSkipped(
            'Password confirmation is not part of this admin-only OTP system.'
        );
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $this->markTestSkipped(
            'Password confirmation is not part of this admin-only OTP system.'
        );
    }
}
