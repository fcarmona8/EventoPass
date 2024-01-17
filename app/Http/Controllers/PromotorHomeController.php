<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PromotorHomeController extends Controller {
    public function index(Request $request) {
        Log::info('Entrando en método index de PromotorHomeController.');

        $user_id = Auth::id();
        $query = Event::query(); 

        $existingAddresses = Venue::where('user_id', $user_id)->get();
        Log::info('Direcciones existentes recuperadas: ', ['existingAddresses' => $existingAddresses]);

        $query->where(function ($q) use ($user_id) {
            $q->userEvent($user_id);
        });

        $events = $query->orderBy('id')->paginate(env('PAGINATION_LIMIT_PROMOTOR', 10));
        Log::info('Eventos recuperados: ', ['events' => $events]);

        return view('promotor/promotorhome', compact('events', 'existingAddresses'));
    }

    public function edit(Request $request) {
        Log::info('Entrando en método edit de PromotorHomeController.', ['request' => $request->all()]);
    
        // Validación de los datos del formulario
        $validatedData = $request->validate([
            'eventId' => 'required',
            'eventName' => 'required|string|max:255',
            'eventDesc' => 'required|string',
            'eventVid' => 'nullable|url',
            'eventAddress' => 'required|integer',
        ]);
    
        $venueId = $request->input('eventAddress');
        $eventId = $request->input('eventId');
        $eventName = $request->input('eventName');
    
        $venue = Venue::find($venueId);
        $event = Event::find($eventId);
    
        if (!$venue) {
            Log::error('Venue no encontrado con ID: ', ['venueId' => $venueId]);
            return response()->json(['error' => 'Venue no encontrado.'], 404);
        }
    
        $event->name = $eventName;
        Log::info('Actualizando evento: ', ['event' => $event]);
    
        try {
            $event->save();
            return response()->json(['event' => $event]);
            Log::info('Evento guardado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al guardar el evento: ', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Error al guardar el evento'], 500);
        }
    }    
}
