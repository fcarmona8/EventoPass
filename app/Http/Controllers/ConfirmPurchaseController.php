<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Session;
use App\Models\Purchase;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfirmPurchaseController extends Controller
{
    public function showConfirmPurchase(Request $request)
    {
        $eventId = $request->input('eventId');
        $totalPrice = $request->input('totalPrice');
        $ticketData = json_decode($request->input('ticketData'), true);
        
        $event = Event::with('venue')->find($eventId);
        $ticketTypes = TicketType::findMany(array_keys($ticketData));

        \Log::info('TicketData: ', $ticketData);
        \Log::info('TicketTypes: ', $ticketTypes->toArray());

        $areTicketsNominal = $event->nominal;
        \Log::info('Valor de $areTicketsNominal: ' . $areTicketsNominal);

        return view('tickets.purchaseconfirm', compact('eventId', 'event', 'totalPrice', 'ticketTypes', 'ticketData', 'areTicketsNominal'));
    }
    
}