<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Mail\mailEntradesCorreu;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\MailController;

class MailControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_tickets_email()
    {
        // Configurar el entorno
        $url = env('url');
        Mail::fake();

        // Datos de prueba
        $correu = 'correo@example.com';
        $namePdf = 'ticket123';
        $nameEvent = 'Evento de prueba';
        $eventId = 123;

        // Ejecutar el método del controlador
        MailController::enviarEntrades($correu, $namePdf, $nameEvent, $eventId);

        // Verificar que se haya enviado el correo electrónico
        Mail::assertSent(mailEntradesCorreu::class, function ($mail) use ($correu, $namePdf, $url, $eventId) {
            return $mail->hasTo($correu) &&
                   $mail->name === $url."/entrades/".$namePdf.".pdf" &&
                   $mail->event === $url.'/tickets/showevent/'.$eventId;
        });
    }
}
