<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\Test\\DatabaseSeeder']);
    }

    // Login con credenciales correctas.
    public function testUserCanLoginWithCorrectCredentials()
    {
        $response = $this->post('/login', [
            'email' => 'promotor1@test.com',
            'password' => 'p12345678',
        ]);

        $response->assertRedirect('/promotor/promotorhome');
        $this->assertAuthenticated();
    }

    // Login con contraseña incorrecta.
    public function testUserCannotLoginWithIncorrectCredentials()
    {
        $response = $this->post('/login', [
            'email' => 'promotor1@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // Login con un email que no existe.
    public function testUserCannotLoginWithNonexistentEmail()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'p12345678',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // Acceso al formulario de login.
    public function testLoginFormCanBeAccessed()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    // Redirección si el usuario ya está logueado.
    public function testUserIsRedirectedIfAlreadyLoggedIn()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/');
    }

    // Login con credenciales vacías.
    public function testUserCannotLoginWithEmptyCredentials()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    // Login solo con email.
    public function testUserCannotLoginWithEmailOnly()
    {
        $response = $this->post('/login', [
            'email' => 'promotor1@test.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

     // Login solo con contraseña.
    public function testUserCannotLoginWithPasswordOnly()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'p12345678',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
