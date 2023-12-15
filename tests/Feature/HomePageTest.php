<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Event;
use App\Models\Category;
use App\Models\Venue;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testHomePageDisplaysEventsWithCorrectLowestTicketPrice()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $event = Event::with(['category', 'venue', 'sessions.purchases.tickets.type'])->first();
        $page = 1;
        $found = false;

        while (!$found) {
            $response = $this->get('/?page=' . $page);

            $response->assertStatus(200);

            if (str_contains($response->content(), e($event->name))) {
                $found = true;
                $response->assertSee(e($event->name), false);
                $response->assertSee(e($event->description), false);
                $response->assertSee(e($event->event_date), false);
                $response->assertSee(e($event->venue->name), false);


                // Calcular el precio mínimo esperado y aserciones
                $expectedLowestPrice = $event->sessions->flatMap(function ($session) {
                    return $session->purchases->flatMap(function ($purchase) {
                        return $purchase->tickets->map(function ($ticket) {
                            return $ticket->type->price;
                        });
                    });
                })->min();

                $actualLowestPrice = $event->lowestTicketPrice();
                $response->assertSee(number_format($actualLowestPrice, 2));
                $this->assertEquals(number_format($expectedLowestPrice, 2), number_format($actualLowestPrice, 2));
            } else {
                $page++;
            }
        }
    }

    public function testHomePageDisplaysEventsWithCorrectCheckFilterForCity(){
        $city = "a";

        // Simular una búsqueda con filtro
        $response = $this->get("/?filtro=ciudad&search={$city}");

        $response->assertStatus(200);

        // Verificar que la página contiene eventos que cumplen con el filtro
        $events = Event::with(['category', 'venue', 'sessions.purchases.tickets.type'])
            ->whereIn('venue_id', function ($subquery) use ($city) {
                $subquery->select('id')
                    ->from('venues')
                    ->where('location', 'ILIKE', "%{$city}%");
            })->get();


        foreach ($events as $event) {
            $response->assertSee(e($event->name));
            $response->assertSee(e($event->description));
        }
    }

    public function testHomePageDisplaysEventsWithCorrectCheckFilterForEvent()
    {
        $eventTitle = "sed";

        // Simular una búsqueda con filtro
        $response = $this->get("/?filtro=evento&search={$eventTitle}");

        $response->assertStatus(200);

        // Verificar que la página contiene eventos que cumplen con el filtro
        $events = Event::with(['category', 'venue', 'sessions.purchases.tickets.type'])
            ->where('name', 'ILIKE', "%{$eventTitle}%")->get();

        foreach ($events as $event) {
            $response->assertSee(e($event->name));
            $response->assertSee(e($event->description));
        }
    }

    public function testHomePageDisplaysEventsWithCorrectCheckFilterForVenue(){
        $venue = "legros";

        // Simular una búsqueda con filtro
        $response = $this->get("/?filtro=ciudad&search={$venue}");

        $response->assertStatus(200);

        // Verificar que la página contiene eventos que cumplen con el filtro
        $events = Event::with(['category', 'venue', 'sessions.purchases.tickets.type'])
            ->whereIn('venue_id', function ($subquery) use ($venue) {
                $subquery->select('id')
                    ->from('venues')
                    ->where('name', 'ILIKE', "%{$venue}%");
            })->get();


        foreach ($events as $event) {
            $response->assertSee(e($event->name));
            $response->assertSee(e($event->description));
        }
    }

}
