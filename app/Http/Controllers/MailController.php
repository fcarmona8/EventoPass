<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\mailEntradesCorreu;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;


class MailController extends Controller{


    public static function enviarEntrades($correu, $namePdf, $nameEvent, $eventId){

        $name = "http://127.0.0.1:8000/entrades/".$namePdf.".pdf";

        $event = "http://127.0.0.1:8000/tickets/showevent/".$eventId;

        Mail::to($correu)->send(new mailEntradesCorreu($name, $nameEvent, $event));
    }


}