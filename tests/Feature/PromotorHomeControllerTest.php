<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class PromotorHomeControllerTest extends TestCase
{
    use RefreshDatabase;

   public function setUp(): void
   {
       parent::setUp();

       $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\Test\\DatabaseSeeder']);
   }

    public function testIndexMethod()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/promotor/home');
        $response->assertStatus(404);
    }

    public function testEditMethod()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $formData = [
            'eventId' => 1,
            'eventName' => 'Nuevo Nombre de Evento',
            'eventDesc' => 'Nueva Descripción de Evento',
            'eventAddress' => 1, 
        ];

        $response = $this->post('/promotor/edit-event', $formData);
        $response->assertStatus(404);
    }
}
