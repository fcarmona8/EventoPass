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

        $response->assertSee(e($event->name));
        $response->assertSee(e($event->description));
        if ($event->main_image) {
            $response->assertSee(asset('storage/' . $event->main_image));
        }

        $response->assertSee(e($event->category->name));
        $response->assertSee(e($event->venue->name));
        $response->assertSee(e($event->event_date));

        // Calcular el precio mínimo esperado
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
    }

}