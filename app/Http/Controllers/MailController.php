<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\mailEntradesCorreu;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;


class MailController extends Controller{


    public static function enviarEntrades($correu, $namePdf, $nameEvent, $eventId){
        $url = env('url');
        $name = $url."/entrades/".$namePdf.".pdf";

        $event = $url."/tickets/showevent/".$eventId;

        Mail::to($correu)->send(new mailEntradesCorreu($name, $nameEvent, $event));
    }


}