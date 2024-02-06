<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Session as sessionLaravel;


class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'total_price', 'name', 'email', 'dni', 'phone'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }


    public function generarCompra($session_id, $total_price, $name, $email, $dni, $phone, $nTickets)
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
        ]);

        $purchase_id = $purchase->id;

        $tickets = self::nEntrades();

        foreach ($tickets as $ticket_id => $quantity) {
            Ticket::buyTicket($session_id, $ticket_id, $purchase_id, $quantity);

            Ticket::restarNTickets($ticket_id, $quantity);
        }

        DB::commit();
    }

    public static function nEntrades()
    {
        $session = sessionLaravel::get('a');
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

}
