<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
   {
       parent::setUp();

       $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\Test\\DatabaseSeeder']);
   }

    /** @test */
    public function it_shows_the_link_request_form()
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.email');
    }

    /** @test */
    public function it_sends_reset_link_email_successfully()
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->post(route('password.email'), [
            'email' => $user->email
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status', trans(Password::RESET_LINK_SENT));

        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\ResetPassword::class);
    }

    /** @test */
    public function it_handles_invalid_email_when_sending_reset_link()
    {
        $response = $this->post(route('password.email'), [
            'email' => 'not-a-valid-email'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_handles_exception_when_sending_reset_link()
    {
        $response = $this->post(route('password.email'), [
            'email' => 'user@example.com'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }
}
