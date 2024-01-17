<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Session;
use App\Models\TicketType;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromotorSessionsListController extends Controller{

    public function index(Request $request)
    {
        $event_id = $request->input('id');
    
        $sessions = Session::with('event')
            ->where('event_id', $event_id)
            ->orderBy('date_time')
            ->get()
            ->map(function ($session) {
                $session->sold_tickets = Ticket::where('session_id', $session->id)->count();
                return $session;
            });
    
        return view('promotor/promotorSessionsList', compact('sessions'));
    }

    public function storeSession(Request $request){

        try {

            $valorNominals = $request->has('nominal_entries');

            $validatedData = $request->validate([
                'data_sesion' => 'required|date',
                'max_capacity' => 'required|numeric|min:1',
                'entry_type_name.*' => 'required|string',
                'entry_type_price.*' => 'required|numeric',
                'entry_type_quantity.*' => 'required|integer',
                'selector-options-sesion' => 'required|integer',
            ]);

            Log::info('Datos de la sesión validados: ' . json_encode($validatedData));

            $selectorOption = $request->input('selector-options-sesion');
            Log::info('Selector Option: ' . $selectorOption);

            $eventDateTime = new \DateTime($validatedData['data_sesion']);
            $onlineSaleEndTime = clone $eventDateTime;

            switch ($selectorOption) {
                case "1":
                    $onlineSaleEndTime;
                    break;
                case "2":
                    $onlineSaleEndTime->modify('-1 hour');
                    break;
                case "3":
                    $onlineSaleEndTime->modify('-2 hours');
                    break;
            }

            $event_id = 1;
        
            $session = new Session([
                'event_id' => $event_id,
                'date_time' => $eventDateTime,
                'max_capacity' => $validatedData['max_capacity'],
                'online_sale_end_time' => $onlineSaleEndTime,    
                'named_tickets' => $valorNominals
            ]);

            $session->save();

            $typeNames = $request->input('entry_type_name');
            $typePrices = $request->input('entry_type_price');
            $typeQuantities = $request->input('entry_type_quantity');

            if (is_array($typeNames) && is_array($typePrices) && is_array($typeQuantities)) {
                foreach ($typeNames as $index => $name) {
                    $ticketType = new TicketType([
                        'name' => $name,
                        'price' => $typePrices[$index],
                        'available_tickets' => $typeQuantities[$index],
                    ]);

                    $ticketType->save();

                    for ($i = 0; $i < $ticketType->available_tickets; $i++) {
                        $ticket = new Ticket([
                            'type_id' => $ticketType->id,
                            'session_id' => $session->id,
                        ]);

                        $ticket->save();
                    }
                }
            }
    
            return response()->json(['message' => 'Sesion guardada correctamente']);
        } catch (\Exception $e) {
            Log::error('Error en el proceso de almacenamiento de la sesión: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar la sesión.']);
        }
    }

}