<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_register_with_full_profile(): void
    {
        $response = $this->post('/register', [
            'name' => 'Sara Bennani',
            'email' => 'Sara.Bennani@Example.com',
            'cne' => 'd13009988 ',
            'birthday_day' => 15,
            'birthday_month' => 4,
            'birthday_year' => 2002,
            'filiere' => 'Cycle Ingénieur',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'name' => 'Sara Bennani',
            'email' => 'sara.bennani@example.com',
            'cne' => 'D13009988',
            'birthday' => '2002-04-15 00:00:00',
            'filiere' => 'Cycle Ingénieur',
        ]);
    }

    public function test_users_can_register_with_composed_birthday_string(): void
    {
        $response = $this->post('/register', [
            'name' => 'Omar Fassi',
            'email' => 'omar@example.com',
            'cne' => 'OM776655',
            'birthday' => '2001-11-20',
            'filiere' => 'Master',
            'password' => 'Pass12345!',
            'password_confirmation' => 'Pass12345!',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'omar@example.com',
            'birthday' => '2001-11-20 00:00:00',
        ]);
    }

    public function test_invalid_calendar_date_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name' => 'Invalid Date User',
            'email' => 'invalid@example.com',
            'birthday_day' => 31,
            'birthday_month' => 2, // Feb 31 does not exist
            'birthday_year' => 2002,
            'password' => 'Pass12345!',
            'password_confirmation' => 'Pass12345!',
        ]);

        $response->assertSessionHasErrors('birthday_day');
    }

    public function test_underage_birthday_is_rejected(): void
    {
        $currentYear = (int) date('Y');
        $response = $this->post('/register', [
            'name' => 'Underage User',
            'email' => 'underage@example.com',
            'birthday_day' => 1,
            'birthday_month' => 1,
            'birthday_year' => $currentYear - 10, // 10 years old
            'password' => 'Pass12345!',
            'password_confirmation' => 'Pass12345!',
        ]);

        $response->assertSessionHasErrors('birthday_year');
    }
}
