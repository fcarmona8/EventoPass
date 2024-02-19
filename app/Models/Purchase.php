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

        $tickets = self::nEntrades();

        $session = LaravelSession::get('datosCompra');

        $num=1;

        foreach ($tickets as $ticket_id) {

            if (isset($session['name'.$num]) && !is_null($session['name'.$num])) {
                $name = $session['name'.$num];
            }else{
                $name = $session['buyerName'];
            }

            $idEntrada = hash('sha256',$name.$ticket_id.$num.$session['sessionId'].rand(1, 100).$session['buyerDNI'].$ticket_id.$num);
            $session['unicIdNameTicket'.$num] = $idEntrada;

            LaravelSession::put('datosCompra', $session);

            Ticket::buyTicket($session_id, $ticket_id, $purchase_id, $name, $idEntrada);

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

    public static function nEntrades()
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

}
