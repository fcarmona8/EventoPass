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
}