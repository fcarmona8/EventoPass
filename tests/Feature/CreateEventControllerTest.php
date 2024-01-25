<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CreateEventController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Venue;
use App\Models\Event;
use App\Models\User;
use Mockery;

class CreateEventControllerTest extends TestCase
{
     use RefreshDatabase;
     use WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\Test\\DatabaseSeeder']);
    }

    /** @test */
    public function testCreate()
    {
        $categories = Category::factory()->count(2)->create();

        $fakeUserId = 1;
        Auth::shouldReceive('id')->once()->andReturn($fakeUserId);

        $fakeVenues = Venue::factory()->count(5)->create(['user_id' => $fakeUserId]);

        $controller = new CreateEventController();
        $response = $controller->create();

        $this->assertEquals('promotor.createEvent', $response->getName());
        $this->assertArrayHasKey('existingAddresses', $response->getData());
        $this->assertArrayHasKey('categories', $response->getData());
    }

    /** @test */
    public function event_is_successfully_created()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create();
        $venue = Venue::factory()->create(['user_id' => $user->id]);

        $response = $this->post(route('promotor.storeEvent'), [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'event_datetime' => now()->addWeek()->format('Y-m-d H:i:s'),
            'event_image' => UploadedFile::fake()->image('event.jpg'),
            'selector-options-categoria' => (string)$category->id,
            'max_capacity' => 100,
            'promo_video_link' => 'http://example.com/video',
            'event_hidden' => false,
            'selector-options' => $venue->id,
        ]);

        $response->assertRedirect('/promotor/promotorhome');
        $this->assertDatabaseHas('events', ['name' => 'Test Event']);
    }

    /** @test */
    public function only_authenticated_users_can_create_events()
    {
        $response = $this->post(route('promotor.storeEvent'), [
        ]);

        $response->assertRedirect('/');
    }

    /** @test */
    public function testStoreWithInvalidData()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('promotor.storeEvent'), [
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function testStoreVenue()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('promotor.createVenue'), [
            'nova_provincia' => 'Barcelona',
            'nova_ciutat' => 'Barcelona',
            'codi_postal' => '08001',
            'nom_local' => 'Local de Prueba',
            'capacitat_local' => 100,
        ]);

        $response->assertJson(['message' => 'Dirección guardada correctamente']);
    }

    /** @test */
    public function store_venue_with_exception()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('promotor.createVenue'), [
            'nova_provincia' => null,
            'nova_ciutat' => 'Barcelona',
            'codi_postal' => '08001',
            'nom_local' => 'Local de Prueba',
            'capacitat_local' => 100,
        ]);

        $response->assertSessionHasErrors(['error']);
    }

}
