<?php

namespace App\Console\Commands;

use App\Mail\mailRecordatorioEvento;
use App\Models\Purchase;
use Carbon\Carbon;
use App\Models\Session;
use App\Mail\TuMailable;
use App\Models\TuModelo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckDatabaseAndSendEmail extends Command
{
    protected $signature = 'email:check';

    protected $description = 'Comprobar fecha en la base de datos y enviar correo si es para el día siguiente';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $fechaSiguiente = Carbon::now()->addDay()->startOfDay();
        $resultados = Session::whereDate('date_time', '=', $fechaSiguiente)->get();

        $comprasTotales = [];

        foreach ($resultados as $resultado) {
            $compras = Purchase::where('session_id', '=', $resultado->id)->get();

            $comprasTotales = array_merge($comprasTotales, $compras->toArray());
        }

        foreach ($comprasTotales as $compra) {
            //dd($compra);
            Mail::to($compra['email'])->send(new mailRecordatorioEvento($compra['ticketsPDF']));
        }

    }
}
