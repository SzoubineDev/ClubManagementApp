<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordRecoveryTest extends TestCase
{
    use RefreshDatabase;

    private function verifiedUser(): User
    {
        return User::factory()->create([
            'email' => 'student@club.test',
            'cne' => 'AB12345',
            'birthday' => '2001-04-15',
        ]);
    }

    public function test_recovery_screen_can_be_rendered(): void
    {
        $this->get('/password/recover')->assertStatus(200);
        // Legacy Breeze URL still resolves into the new flow.
        $this->get('/forgot-password')->assertStatus(200);
    }

    public function test_identity_verification_grants_reset_access(): void
    {
        $this->verifiedUser();

        $response = $this->post('/password/recover', [
            'email' => 'Student@Club.test',   // mixed case → normalized
            'cne' => 'ab12345 ',              // lowercase + trailing space → normalized
            'birthday' => '2001-04-15',
        ]);

        $response->assertRedirect(route('password.reset.form'));
        $response->assertSessionHas('pw_reset_verified');
    }

    public function test_wrong_birthday_does_not_permit_reset(): void
    {
        $this->verifiedUser();

        $response = $this->post('/password/recover', [
            'email' => 'student@club.test',
            'cne' => 'AB12345',
            'birthday' => '2001-04-16',
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertSessionMissing('pw_reset_verified');

        // Generic message: must never reveal which field was wrong.
        $errors = session('errors')->all();
        $this->assertCount(1, $errors);
    }

    public function test_wrong_cne_does_not_permit_reset(): void
    {
        $this->verifiedUser();

        $this->post('/password/recover', [
            'email' => 'student@club.test',
            'cne' => 'ZZ99999',
            'birthday' => '2001-04-15',
        ])->assertSessionHasErrors('email');
    }

    public function test_suspended_accounts_cannot_recover(): void
    {
        $user = $this->verifiedUser();
        $user->forceFill(['is_suspended' => true])->save();

        $this->post('/password/recover', [
            'email' => 'student@club.test',
            'cne' => 'AB12345',
            'birthday' => '2001-04-15',
        ])->assertSessionHasErrors('email');
    }

    public function test_reset_form_requires_grant(): void
    {
        $this->get('/password/reset')
            ->assertRedirect(route('password.recover'))
            ->assertSessionHasErrors('email');
    }

    public function test_password_can_be_changed_after_verification(): void
    {
        $user = $this->verifiedUser();

        $this->post('/password/recover', [
            'email' => 'student@club.test',
            'cne' => 'AB12345',
            'birthday' => '2001-04-15',
        ]);

        $grant = session('pw_reset_verified');

        $response = $this->post('/password/reset', [
            'token' => $grant['token'],
            'email' => $grant['email'],
            'password' => 'SuperSecret#2026',
            'password_confirmation' => 'SuperSecret#2026',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionMissing('pw_reset_verified');

        $this->assertNotSame($user->fresh()->password, $user->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check(
            'SuperSecret#2026', $user->fresh()->password
        ));
    }

    public function test_grant_cannot_be_reused(): void
    {
        $this->verifiedUser();

        $this->post('/password/recover', [
            'email' => 'student@club.test',
            'cne' => 'AB12345',
            'birthday' => '2001-04-15',
        ]);

        $grant = session('pw_reset_verified');

        $this->post('/password/reset', [
            'token' => $grant['token'],
            'email' => $grant['email'],
            'password' => 'SuperSecret#2026',
            'password_confirmation' => 'SuperSecret#2026',
        ])->assertRedirect(route('login'));

        // Second attempt with the same (burned) grant → bounced back.
        $this->post('/password/reset', [
            'token' => $grant['token'],
            'email' => $grant['email'],
            'password' => 'AnotherSecret#1',
            'password_confirmation' => 'AnotherSecret#1',
        ])->assertRedirect(route('password.recover'));
    }

    public function test_expired_grant_is_rejected(): void
    {
        $this->verifiedUser();

        $this->post('/password/recover', [
            'email' => 'student@club.test',
            'cne' => 'AB12345',
            'birthday' => '2001-04-15',
        ]);

        // Forge an expired grant in the session.
        $grant = session('pw_reset_verified');
        $grant['expires_at'] = now()->subMinutes(30)->timestamp;
        session()->put('pw_reset_verified', $grant);

        $this->post('/password/reset', [
            'token' => $grant['token'],
            'email' => $grant['email'],
            'password' => 'SuperSecret#2026',
            'password_confirmation' => 'SuperSecret#2026',
        ])->assertRedirect(route('password.recover'));
    }

    public function test_manipulated_email_cannot_target_another_account(): void
    {
        $victim = $this->verifiedUser();
        $attacker = User::factory()->create([
            'email' => 'attacker@club.test',
            'cne' => 'CD99999',
            'birthday' => '2000-01-01',
        ]);

        // Attacker verifies their OWN identity…
        $this->post('/password/recover', [
            'email' => 'attacker@club.test',
            'cne' => 'CD99999',
            'birthday' => '2000-01-01',
        ]);
        $grant = session('pw_reset_verified');

        // …then swaps the email to the victim while keeping their own token.
        $this->post('/password/reset', [
            'token' => $grant['token'],
            'email' => 'student@club.test',
            'password' => 'Hijacked#2026',
            'password_confirmation' => 'Hijacked#2026',
        ])->assertSessionHasErrors('token');

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check(
            'password', $victim->fresh()->password
        ));
    }

    public function test_verification_is_rate_limited_per_account(): void
    {
        $this->verifiedUser();

        $payload = [
            'email' => 'student@club.test',
            'cne' => 'WRONGCNE',
            'birthday' => '2001-04-15',
        ];

        // 5 allowed failures per hour (controller limit).
        for ($i = 0; $i < 5; $i++) {
            $this->post('/password/recover', $payload)
                ->assertSessionHasErrors('email');
        }

        // 6th attempt → throttled, generic "try again later" message.
        $response = $this->post('/password/recover', array_merge($payload, ['cne' => 'AB12345']));
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'Too many verification attempts',
            session('errors')->first('email')
        );
    }
}
