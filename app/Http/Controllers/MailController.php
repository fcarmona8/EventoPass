<?php

namespace App\Http\Controllers;

use App\Mail\mailEntradesCorreu;
use Illuminate\Support\Facades\Mail;


class MailController extends Controller{


    public function enviarEntrades(){

        $name = "http://127.0.0.1:8000/b/a.pdf";
        $nombreEvento = "Evento 5";

        Mail::to('hola@gmail.com')->send(new mailEntradesCorreu($name, $nombreEvento));
    }


}