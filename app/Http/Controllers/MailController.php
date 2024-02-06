<?php

namespace App\Http\Controllers;

use App\Mail\mailEntradesCorreu;
use Illuminate\Support\Facades\Mail;


class MailController extends Controller{


    public static function enviarEntrades($correu, $namePdf, $nameEvent){

        $name = "http://127.0.0.1:8000/entrades/".$namePdf.".pdf";

        Mail::to($correu)->send(new mailEntradesCorreu($name, $nameEvent));
    }


}