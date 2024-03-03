<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Session as LaravelSession;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'total_price', 'name', 'email', 'dni', 'phone', 'ticketsPDF'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }


    public function generarCompra($session_id, $total_price, $name, $email, $dni, $phone, $nTickets, $namePDF)
    {
        DB::beginTransaction();

        // Crea una nueva compra
        $purchase = Purchase::create([
            'session_id' => $session_id,
            'total_price' => $total_price,
            'name' => $name,
            'email' => $email,
            'dni' => $dni,
            'phone' => $phone,
            'ticketsPDF' => $namePDF
        ]);


        $purchase_id = $purchase->id;
        $session = LaravelSession::get('datosCompra');

        if($session['nominals?']== true){
            $tickets = self::nEntradesNominal();
        }else{
            $tickets = self::nEntrades();
        }

        $num=1;

        foreach ($tickets as $ticket_id) {

            if (isset($session['name'.$num]) && !is_null($session['name'.$num])) {
                $name = $session['name'.$num];
                $dniNominal = $session['dni'.$num];
                $telefono = $session['phone'.$num];
            }else{
                $name = null;
                $dniNominal = null;
                $telefono = null;
            }

            $idEntrada = hash('sha256',$name.$ticket_id.$num.$session['sessionId'].rand(1, 100).$session['buyerDNI'].$ticket_id.$num);
            $session['unicIdNameTicket'.$num] = $idEntrada;

            LaravelSession::put('datosCompra', $session);

            $buyerName = $session['buyerName'];

            Ticket::buyTicket($session_id, $ticket_id, $purchase_id, $name, $dniNominal, $telefono, $idEntrada, $buyerName);

            Ticket::restarNTickets($ticket_id, 1);
            $num++;
        }

        DB::commit();
    }

    public static function nEntradesAgrupadas()
    {
        $session = LaravelSession::get('datosCompra');
        
        $tickets = [];

        foreach ($session as $key => $value) {
            if (strpos($key, 'ticketNameId') === 0) {
                $num = substr($key, strlen('ticketNameId'));
                $ticketId = $value;
                $ticketNameNumKey = "ticketNameNum$num";
                if (isset($session[$ticketNameNumKey])) {
                    $ticketNameNum = $session[$ticketNameNumKey];
                    $tickets[$ticketId] = $ticketNameNum;
                }
            }
        }

        return $tickets;
    }

    public static function nEntradesNominal()
{
    $session = LaravelSession::get('datosCompra');

    $tickets = [];

    foreach ($session as $key => $value) {
        // Verificar si la clave comienza con 'ticketNameNum'
        if (strpos($key, 'ticketNameId') === 0) {
            // Almacenar el número de entradas en el arreglo
            $tickets[] = $value;
        }
    }

    return $tickets;
}

public static function nEntrades()
{
    $session = LaravelSession::get('datosCompra');
    $tickets = [];

    foreach ($session as $key => $value) {
        // Verificar si la clave comienza con 'ticketNameNum'
        if (strpos($key, 'ticketNameNum') === 0) {
            // Obtener el número de entradas de este tipo
            $numEntradas = intval($value);
            
            // Obtener el ID del ticket correspondiente
            $num = substr($key, strlen('ticketNameNum'));
            $ticketIdKey = "ticketNameId$num";
            $ticketId = $session[$ticketIdKey];
            
            // Agregar el ID del ticket al arreglo $tickets según la cantidad de entradas
            for ($i = 0; $i < $numEntradas; $i++) {
                $tickets[] = $ticketId;
            }
        }
    }

    return $tickets;
}

}
