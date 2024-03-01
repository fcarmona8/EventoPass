<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\mailEntradesCorreu;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;


class MailController extends Controller{

    /**
     * Envia un correo electrónico al usuario con el enlace a las entradas del evento.
     * Utiliza una clase mailable para configurar los datos del correo y enviarlo al email especificado.
     * 
     * @param string $correu
     * @param string $namePdf
     * @param string $nameEvent
     * @param int $eventId
     */
    public static function enviarEntrades($correu, $namePdf, $nameEvent, $eventId){
        $url = env('url');
        $name = $url."/entrades/".$namePdf.".pdf";

        $event = $url."/tickets/showevent/".$eventId;

        Mail::to($correu)->send(new mailEntradesCorreu($name, $nameEvent, $event));
    }
}