<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'type_id', 'session_id', 'is_validated', 'name', 'dni', 'telefono', 'unicIdTicket', 'buyerName'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function type()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }


    public static function buyTicket($session_id, $type_id, $purchase_id, $name, $dni, $telefono, $idEntrada, $buyerName){

        DB::table('tickets')
        ->where('session_id', '=', $session_id)
        ->where('type_id', '=', $type_id)
        ->whereNull('purchase_id') 
        ->take(1) 
        ->update(['purchase_id' => $purchase_id, 'name' => $name, 'dni' => $dni, 'telefono' => $telefono, 'unicIdTicket' => $idEntrada, 'buyerName' => $buyerName]);

    }

    public static function restarNTickets($idTicket, $cantidad){

        $currentAvailableTickets = DB::table('ticket_types')
            ->where('id', '=', $idTicket)
            ->value('available_tickets');

        $newAvailableTickets = $currentAvailableTickets - $cantidad;

        DB::table('ticket_types')
            ->where('id', '=', $idTicket)
            ->update(['available_tickets' => $newAvailableTickets]);
    }
}
