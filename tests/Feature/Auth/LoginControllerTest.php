<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;


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

    // Redirección si el usuario con rol de promotor ya está logueado.
    public function testPromotorIsRedirectedToPromotorHomeIfLoggedIn()
    {
        $promotor = User::factory()->create();

        $response = $this->actingAs($promotor)->get('/login');
        $response->assertRedirect('/');
    }

    // Redirección si el usuario con rol de administrador ya está logueado.
    public function testAdminIsRedirectedToAdminRouteIfLoggedIn()
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/login');
        $response->assertRedirect('/');
    }

    // Inicio de sesión exitoso como administrador
    public function testAdminLoginRedirectsToAdminRoute()
    {
        $adminRoleId = Role::where('name', 'administrador')->first()->id;

        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('adminpassword'),
            'role_id' => $adminRoleId
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'adminpassword',
        ]);

        $response->assertRedirect('/admin/home');
    }

    // Inicio de sesión exitoso como promotor
    public function testPromotorLoginRedirectsToPromotorHome()
    {
        $promotor = User::factory()->create([
            'email' => 'promotor@test.com',
            'password' => bcrypt('promotorpassword'),
        ]);

        $response = $this->post('/login', [
            'email' => 'promotor@test.com',
            'password' => 'promotorpassword',
        ]);

        $response->assertRedirect('/');
    }

    public function testPromotorIsRedirectedToPromotorHomeWhenAccessingLoginForm()
    {

        $promotorRoleId = Role::where('name', 'promotor')->first()->id;
        $promotor = User::factory()->create([
            'role_id' => $promotorRoleId
        ]);

        $response = $this->actingAs($promotor)->get('/login');
        $response->assertRedirect('/promotor/promotorhome');
    }

    public function testAdminIsRedirectedToAdminRouteWhenAccessingLoginForm()
    {
        $adminRoleId = Role::where('name', 'administrador')->first()->id;
        $admin = User::factory()->create([
            'role_id' => $adminRoleId
        ]);

        $response = $this->actingAs($admin)->get('/login');
        $response->assertRedirect('/admin/home');
    }

    public function testUserWithNoSpecificRoleIsRedirectedToIntended()
    {
        $user = User::factory()->create([
            'email' => 'genericuser@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get('/promotor/promotorhome');
        $response->assertRedirect('/login');

        $response = $this->post('/login', [
            'email' => 'genericuser@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/promotor/promotorhome');
    }
}
