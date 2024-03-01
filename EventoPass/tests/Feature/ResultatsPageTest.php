<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Event;
use App\Models\Category;
use App\Models\Venue;

class ResultatsPageTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\Test\\DatabaseSeeder']);
    }

    public function testResultatsPageDisplaysEventsWithCorrectLowestTicketPrice()
    {
        $response = $this->get('/resultats');

        $response->assertStatus(200);

        $event = Event::with(['category', 'venue', 'sessions.purchases.tickets.type'])->first();
        $page = 1;
        $found = false;

        while (!$found) {
            $response = $this->get('/resultats?page=' . $page);

            $response->assertStatus(200);

            if (str_contains($response->content(), e($event->name))) {
                $found = true;
                $response->assertSee(e($event->name), false);
                $response->assertSee(e($event->description), false);
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

    public function testResultatsPageDisplaysEventsWithCorrectCheckFilterForCity()
    {
        $city = "a";
        // Simular una búsqueda con filtro y paginación
        $response = $this->get("/resultats?filtro=ciudad&search={$city}");

        $response->assertStatus(200);

        // Verificar que la página contiene eventos que cumplen con el filtro
        $q = Event::with(['category', 'venue', 'sessions.purchases.tickets.type'])
            ->whereIn('venue_id', function ($subquery) use ($city) {
                $subquery->select('id')
                    ->from('venues')
                    ->where('location', 'ILIKE', "%{$city}%");
            });

        $events = $q->orderBy('event_date')->take(env('PAGINATION_LIMIT'));

        foreach ($events as $event) {
            $response->assertSeeText(e($event->name));
            $response->assertSee(e($event->description));
        }
    }

    public function testResultatsPageDisplaysEventsWithCorrectCheckFilterForEvent()
    {
        $eventTitle = "sed";

        // Simular una búsqueda con filtro
        $response = $this->get("/resultats?filtro=evento&search={$eventTitle}");

        $response->assertStatus(200);

        // Verificar que la página contiene eventos que cumplen con el filtro
        $q = Event::with(['category', 'venue', 'sessions.purchases.tickets.type'])
            ->where('name', 'ILIKE', "%{$eventTitle}%");

            $events = $q->orderBy('event_date')->take(env('PAGINATION_LIMIT'));

        foreach ($events as $event) {
            $response->assertSee(e($event->name));
            $response->assertSee(e($event->description));
        }
    }

    public function testResultatsPageDisplaysEventsWithCorrectCheckFilterForVenue()
    {
        $venue = "legros";

        // Simular una búsqueda con filtro
        $response = $this->get("/resultats?filtro=ciudad&search={$venue}");

        $response->assertStatus(200);

        // Verificar que la página contiene eventos que cumplen con el filtro
        $q = Event::with(['category', 'venue', 'sessions.purchases.tickets.type'])
            ->whereIn('venue_id', function ($subquery) use ($venue) {
                $subquery->select('id')
                    ->from('venues')
                    ->where('name', 'ILIKE', "%{$venue}%");
            });

            $events = $q->orderBy('event_date')->take(env('PAGINATION_LIMIT'));

        foreach ($events as $event) {
            $response->assertSee(e($event->name));
            $response->assertSee(e($event->description));
        }
    }

    public function testResultatsPageDisplaysEventsWithCorrectCheckCategory()
    {
        $category = 190;
    
        // Simular una búsqueda con filtro
        $response = $this->get("/resultats?filtro=recinto&search=a&categoria={$category}");
        $response->assertStatus(200);
    
        // Verificar que la página contiene eventos que cumplen con el filtro
        $q = Event::with(['category', 'venue', 'sessions.purchases.tickets.type'])
            ->whereIn('category_id', function ($q) use ($category) {
                $q->select('id')
                    ->from('categories')
                    ->where('name', 'LIKE', "{$category}")
                    ->where('hidden', '=', 'false');
            });
    
        $events = $q->orderBy('event_date')->take(env('PAGINATION_LIMIT'));
    
        foreach ($events as $event) {
            $response->assertSee(e($event->name));
            $response->assertSee(e($event->description));
            $response->assertSee(e($event->category_id));
        }
    }
    

}
