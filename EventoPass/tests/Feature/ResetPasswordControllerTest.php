<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\Test\\DatabaseSeeder']);
    }

    public function testShowResetFormWithValidToken()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        
        $token = Password::broker()->createToken($user);

        DB::table('password_reset_tokens')->where('email', '=', 'test@example.com')->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $response = $this->get(route('password.reset', ['token' => $token, 'email' => 'test@example.com']));

        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.reset');
    }

    public function testShowResetFormWithInvalidToken()
    {
        $response = $this->get(route('password.reset', ['token' => 'invalidtoken', 'email' => 'test@example.com']));
        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.token_expired');
    }

    public function testResetPasswordSuccess()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!'
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('NewPassword1!', $user->fresh()->password));
    }

    public function testResetPasswordFailure()
    {
        $response = $this->post(route('password.update'), [
            'token' => 'invalidtoken',
            'email' => 'test@example.com',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!'
        ]);

        $response->assertSessionHasErrors('email');
    }

}