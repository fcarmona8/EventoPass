<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_form_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Iniciar Sesión');
        $response->assertSee('Correo Electrónico:');
        $response->assertSee('Contraseña:');
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->promoter()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'p12345678'
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    /** @test */
    public function user_cannot_login_with_incorrect_credentials()
    {
        $user = User::factory()->promoter()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword'
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }
}
