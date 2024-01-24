<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Event;
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


    public function testShowFunctionDisplaysEventDetails()
    {
        // Obtén un evento de la base de datos de prueba
        $event = Event::factory()->create();

        // Simula la solicitud GET a la página de detalles del evento
        $response = $this->get("/tickets/showevent/{$event->id}");

        // Verifica que la página se cargue correctamente
        $response->assertStatus(200);

        // Verifica que la información del evento se muestra en la página
        $response->assertSee(e($event->name));
        $response->assertSee(e($event->description));
        $response->assertSee(e($event->venue->name));

        // Verifica que las sesiones y sus detalles se muestran correctamente
        foreach ($event->sessions as $session) {
            $response->assertSee(e($session->formattedDateTime));
            foreach ($session->ticketTypes as $ticketType) {
                $response->assertSee(e($ticketType->name));
            }
        }
    }

    public function testShowFunctionRedirectsIfEventNotFound()
    {
        // Simula la solicitud GET a la página de detalles del evento con un ID no existente
        $response = $this->get("/tickets/showevent/999");

        // Verifica que la respuesta sea una redirección a la página de inicio
        $response->assertRedirect(route('home'));

        // Verifica que se muestre un mensaje de error
        $response->assertSessionHas('error', 'Evento no encontrado.');
    }

    // Agrega más pruebas según sea necesario
}
