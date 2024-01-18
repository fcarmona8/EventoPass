<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CreateEventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Venue;
use Mockery;

class CreateEventControllerTest extends TestCase
{
    use RefreshDatabase;

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

    // public function testStore()
    // {
    //     $this->refreshDatabase();

    //     $category = Category::factory()->create();
    //     $venue = Venue::factory()->create();

    //     $request = new \Illuminate\Http\Request([
    //         'title' => 'Evento de prueba',
    //         'selector-options-categoria' => '1',
    //         'description' => 'Descripción del evento',
    //         'event_datetime' => '2024-02-01 20:00:00',
    //         'event_image' => 'path/to/image.jpg',
    //         'max_capacity' => 100,
    //         'promo_video_link' => 'https://example.com/video',
    //         'event_hidden' => false,
    //         'selector-options' => '1',
    //         'entry_type_name' => ['General', 'VIP'],
    //         'entry_type_price' => [100, 200],
    //         'entry_type_quantity' => [50, 50],
    //         'additional_images' => ['path/to/additional1.jpg', 'path/to/additional2.jpg'],
    //     ]);

    //     Auth::shouldReceive('id')->andReturn(1);

    //     $mockCategory = Mockery::mock(Category::class);
    //     $mockCategory->shouldReceive('find')->andReturn(new Category());

    //     $mockVenue = Mockery::mock(Venue::class);
    //     $mockVenue->shouldReceive('find')->andReturn(new Venue());

    //     $this->app->instance(Category::class, $mockCategory);
    //     $this->app->instance(Venue::class, $mockVenue);

    //     Storage::shouldReceive('disk')
    //         ->once()
    //         ->with('public')
    //         ->andReturnSelf()
    //         ->shouldReceive('makeDirectory')
    //         ->once()
    //         ->andReturn(true);

    //     $controller = new CreateEventController();
    //     $response = $controller->store($request);

    //     $this->assertEquals(302, $response->getStatusCode());
    //     $this->assertRedirect($response, route('promotor.createEvent'));
    //     $this->assertSessionHasAll(['success' => 'Evento, sesión y tickets creados con éxito']);
    // }

    // public function testStoreVenue()
    // {
    //     $this->refreshDatabase();

    //     $request = new \Illuminate\Http\Request([
    //         'nova_provincia' => 'Provincia Test',
    //         'nova_ciutat' => 'Ciudad Test',
    //         'codi_postal' => '12345',
    //         'nom_local' => 'Local Test',
    //         'capacitat_local' => 100,
    //     ]);

    //     $user = User::factory()->create();
    //     Auth::shouldReceive('id')->andReturn($user->id);

    //     $controller = new CreateEventController();
    //     $response = $controller->storeVenue($request);

    //     $responseData = $response->getData(true);
    //     $this->assertEquals(200, $response->getStatusCode());
    //     $this->assertEquals('Dirección guardada correctamente', $responseData['message']);
    //     $this->assertIsArray($responseData['addresses']);

    //     $this->assertDatabaseHas('venues', [
    //         'province' => 'Provincia Test',
    //         'city' => 'Ciudad Test',
    //         'postal_code' => '12345',
    //         'venue_name' => 'Local Test',
    //         'capacity' => 100,
    //         'user_id' => $user->id
    //     ]);
    // }

}
