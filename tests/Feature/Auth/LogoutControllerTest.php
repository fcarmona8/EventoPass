<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Mockery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\Test\\DatabaseSeeder']);
    }

    public function testUserCanLogout()
    {
        $user = User::where('email', 'promotor1@test.com')->first();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/promotor/promotorhome');

        $this->assertGuest();
    }

    public function testUserCannotLogoutIfNotAuthenticated()
    {
        $response = $this->post('/logout');

        $response->assertRedirect('/promotor/promotorhome');
        $this->assertGuest();
    }

    public function testUserIsLoggedOutAfterLogout()
    {
        $user = User::where('email', 'promotor1@test.com')->first();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/promotor/promotorhome');
        $this->assertGuest();
    }

    public function testUserIsRedirectedToLoginAfterLogout()
    {
        $user = User::where('email', 'promotor1@test.com')->first();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/promotor/promotorhome');
        $this->assertGuest();

        $this->get('/promotor/promotorhome')->assertRedirect('/login');
    }

}