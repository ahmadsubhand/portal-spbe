<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_access_request_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSee('Permintaan Akses')
            ->assertSee('administrator portal');
    }

    public function test_public_users_can_not_register_an_asn_account(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertMethodNotAllowed();
        $this->assertGuest();
        $this->assertDatabaseMissing(User::class, ['email' => 'test@example.com']);
    }
}
