<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Venue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class PromotorHomeControllerTest extends TestCase
{
    use RefreshDatabase;

   public function setUp(): void
   {
       parent::setUp();

       $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\Test\\DatabaseSeeder']);
   }

    /** @test */
    public function it_shows_promotor_home_page_with_events_and_addresses()
    {
        $user = $this->post('/login', [
            'email' => 'promotor1@test.com',
            'password' => 'p12345678',
        ]);

        $response = $this->get(route('promotorhome'));

        $response->assertStatus(200);
        $response->assertViewIs('promotor.promotorhome');
        $response->assertViewHas(['events', 'existingAddresses']);
    }

    /** @test */
    public function it_updates_event_with_valid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $event = Event::factory()->create(['user_id' => $user->id]);
        $venue = Venue::factory()->create(['user_id' => $user->id]);

        $response = $this->post(route('promotor.editEvent'), [
            'eventId' => $event->id,
            'eventName' => 'Updated Event Name',
            'eventDesc' => 'Updated Event Description',
            'eventVid' => 'http://example.com/video',
            'eventAddress' => $venue->id,
            'eventHidden' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['event' => $event->fresh()->toArray()]);
    }

    /** @test */
    public function it_fails_to_update_event_when_venue_does_not_exist()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $event = Event::factory()->create(['user_id' => $user->id]);

        $response = $this->post(route('promotor.editEvent'), [
            'eventId' => $event->id,
            'eventName' => 'Updated Event Name',
            'eventDesc' => 'Updated Event Description',
            'eventVid' => 'http://example.com/video',
            'eventAddress' => 9999,
            'eventHidden' => false,
        ]);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Venue no encontrado.']);
    }

}
