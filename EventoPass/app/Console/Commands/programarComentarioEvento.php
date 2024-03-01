<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Session;
use App\Models\Purchase;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Mail\mailComentarioEvento;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;


class programarComentarioEvento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:programar-comentario-evento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprobar base de datos y enviar correo a los usuarios de 
                                los eventos del día anterior para dejar un comentario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fechaAnterior = Carbon::now()->subDay()->startOfDay();
        $resultados = Session::whereDate('date_time', '=', $fechaAnterior)->get();

        foreach ($resultados as $resultado) {
            $compras = Purchase::where('session_id', '=', $resultado->id)->get();

            foreach ($compras as $compra) {
                $eventoId = $resultado->event_id;
                $tokenData = $compra->id . '_' . $eventoId;

                $token = hash('sha256', $tokenData);

                $token = substr($token, 0, 32);
    
                $url = env('url') . '/tickets/comentarios/' . $token . '/' . $compra->id . '/' . $eventoId;

                Mail::to($compra['email'])->send(new mailComentarioEvento($url));
            }
        }
    }
}
