<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Session;
use App\Models\Role;

class PromotorSessionsListControllerTest extends TestCase
{
    use RefreshDatabase;

   public function setUp(): void
   {
       parent::setUp();

       $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\Test\\DatabaseSeeder']);
   }

    /** @test */
    public function promotor_can_access_all_their_events_and_sessions()
    {
        $user = User::where('email', 'promotor1@test.com')->first();
        $this->actingAs($user);

        $event = Event::factory()->create(['user_id' => $user->id]);
        $session = Session::factory()->create(['event_id' => $event->id]);

        $response = $this->get(route('promotorsessionslist'));

        $response->assertStatus(200);
        $response->assertViewHas('events');
        $response->assertViewHas('isSpecificEvent', false);
    }

    /** @test */
    public function it_stores_a_new_session_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $event = Event::factory()->create(['user_id' => $user->id]);

        $response = $this->post(route('promotorsessionslist.storeSession'), [
            'id' => $event->id,
            'data_sesion' => now()->addWeek()->format('Y-m-d H:i:s'),
            'max_capacity' => 100,
            'selector-options-sesion' => 1,
            'entry_type_name' => ['General Admission'],
            'entry_type_price' => [10.00],
            'entry_type_quantity' => [50],
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('promotorsessionslist', ['id' => $event->id]));
        $response->assertSessionHas('success', 'Sesión creada con éxito'); 
    }

}