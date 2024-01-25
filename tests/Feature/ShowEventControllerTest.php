<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Session;
use App\Models\TicketType;
use App\Models\Ticket;

class ShowEventControllerTest extends TestCase
{
    use RefreshDatabase;
    public function setUp(): void
   {
       parent::setUp();

       $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\Test\\DatabaseSeeder']);
   }

   public function test_show_event_successfully()
    {
        $event = Event::factory()->create();
        $venue = Venue::factory()->create();
        $event->venue()->associate($venue);
        $event->save();

        $session = Session::factory()->create(['event_id' => $event->id]);
        $ticketType = TicketType::factory()->create();
        $ticket = Ticket::factory()->create(['session_id' => $session->id, 'type_id' => $ticketType->id]);

        $response = $this->get(route('tickets.showevent', ['id' => $event->id]));
        $response->assertStatus(200);
        $response->assertViewHas('event');
        $response->assertViewHas('formattedSessions');
        $response->assertViewHas('coordinates');

    }

    public function test_show_event_not_found()
    {
        $response = $this->get(route('tickets.showevent', ['id' => 999]));
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Evento no encontrado.');

    }

}
