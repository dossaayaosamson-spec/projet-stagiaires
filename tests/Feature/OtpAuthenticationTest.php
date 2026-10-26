<?php

namespace Tests\Feature;

use App\Mail\SendOtpCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_to_otp_form_and_sends_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login.otp'));
        $this->assertGuest(); // Non connecté tant que l'OTP n'est pas validé

        $user->refresh();
        $this->assertNotNull($user->otp_code);
        $this->assertEquals(6, strlen($user->otp_code));

        Mail::assertSent(SendOtpCode::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->code === $user->otp_code;
        });
    }

    public function test_user_can_authenticate_with_valid_otp_code(): void
    {
        $user = User::factory()->create([
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->withSession(['2fa_user_id' => $user->id])
            ->post(route('login.otp'), [
                'otp_code' => '123456',
            ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertNull($user->otp_code); // Effacé après validation
    }

    public function test_user_cannot_authenticate_with_invalid_otp_code(): void
    {
        $user = User::factory()->create([
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->withSession(['2fa_user_id' => $user->id])
            ->post(route('login.otp'), [
                'otp_code' => '999999',
            ]);

        $response->assertSessionHasErrors('otp_code');
        $this->assertGuest();
    }

    public function test_expired_otp_code_is_rejected(): void
    {
        $user = User::factory()->create([
            'otp_code' => '123456',
            'otp_expires_at' => now()->subMinute(), // Expiré
        ]);

        $response = $this->withSession(['2fa_user_id' => $user->id])
            ->post(route('login.otp'), [
                'otp_code' => '123456',
            ]);

        $response->assertSessionHasErrors('otp_code');
        $this->assertGuest();
    }
}
