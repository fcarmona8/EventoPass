<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Event;
use App\Models\Session;
use App\Models\TicketType;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PromotorSessionsListTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Realiza las migraciones y siembra la base de datos de prueba
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\Test\\DatabaseSeeder']);
    }

    public function testIndexFunctionDisplaysSessionsList()
    {
        // Simula la autenticación del usuario
        $user = Auth::loginUsingId(1);

        // Crea un evento para el usuario autenticado
        $event = Event::factory()->create(['user_id' => $user->id]);

        // Crea algunas sesiones y tickets para el evento
        $sessions = Session::factory()->count(3)->create(['event_id' => $event->id]);
        $sessionIds = $sessions->pluck('id')->toArray();

        Ticket::factory()->count(5)->create(['session_id' => $sessionIds[0]]);
        Ticket::factory()->count(8)->create(['session_id' => $sessionIds[1], 'purchase_id' => 1]);
        Ticket::factory()->count(10)->create(['session_id' => $sessionIds[2], 'purchase_id' => 1]);

        // Simula la solicitud GET a la página de lista de sesiones sin proporcionar event_id
        $responseWithoutEventId = $this->get("/promotor/promotorsessionlist");

        // Verifica que la página se cargue correctamente
        $responseWithoutEventId->assertStatus(200);

        // Verifica que la información de los eventos y las sesiones se muestra en la página
        foreach ($sessions as $session) {
            $responseWithoutEventId->assertSee(e($session->formattedDateTime));
            $responseWithoutEventId->assertSee((string) $session->sold_tickets);
        }

        // Simula la solicitud GET a la página de lista de sesiones proporcionando event_id
        $responseWithEventId = $this->get("/promotor/promotorsessionlist?id={$event->id}");

        // Verifica que la página se cargue correctamente
        $responseWithEventId->assertStatus(200);

        // Verifica que la información del evento y las sesiones se muestra en la página
        $responseWithEventId->assertSee(e($event->name));
        foreach ($sessions as $session) {
            $responseWithEventId->assertSee(e($session->formattedDateTime));
            $responseWithEventId->assertSee((string) $session->sold_tickets);
        }
    }


    public function testStoreSessionFunctionSuccessfullyStoresSession()
    {
        // Simula la autenticación del usuario
        $user = Auth::loginUsingId(1);

        // Crea un evento para el usuario autenticado
        $event = Event::factory()->create(['user_id' => $user->id]);

        // Simula la solicitud POST para almacenar una nueva sesión
        $this->post("/promotor/promotorsessionlist", [
            'event_id' => $event->id,
            'date_time' => now()->addWeek()->format('Y-m-d H:i:s'),
            'max_capacity' => 100,
            'named_tickets' => false,
            'ticket_quantity' => 100,
            'online_sale_end_time' => now()->addWeek()->format('Y-m-d H:i:s'),
        ]);


        // Verifica que la sesión se almacene correctamente en la base de datos
        $latestSession = Session::latest()->first();
        $this->assertEquals($latestSession->id, 1);
    }


    public function testStoreSessionFunctionHandlesErrors()
    {
        // Simula la autenticación del usuario
        $user = Auth::loginUsingId(1);

        // Crea un evento para el usuario autenticado
        $event = Event::factory()->create(['user_id' => $user->id]);

        // Simula la solicitud POST para almacenar una nueva sesión con datos inválidos
        $response = $this->post("/promotor/promotorsessionlist", [
            'id' => $event->id,
        ]);

        // Verifica que la respuesta redirige de nuevo a la página anterior con errores
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

}
