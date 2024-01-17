<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

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
            'eventPhoto' => 'nullable|image',
            'eventHidden' => 'nullable'
        ]);
    
        $venueId = $request->input('eventAddress');
        $eventId = $request->input('eventId');
        $eventName = $request->input('eventName');
        $eventDesc = $request->input('eventDesc');
        $eventVid = $request->input('eventVid');
        $eventHidden = $request->input('eventHidden');

        if($eventHidden == null){
            $eventHidden = 1;
        }
    
        $venue = Venue::find($venueId);
        $event = Event::find($eventId);
    
        if (!$venue) {
            Log::error('Venue no encontrado con ID: ', ['venueId' => $venueId]);
            return response()->json(['error' => 'Venue no encontrado.'], 404);
        }

        // Crear directorio base para el evento
        $eventDirectory = 'event_' . $event->id;
        Storage::disk('public')->makeDirectory($eventDirectory);

        // Guardar imagen principal en subcarpeta 'main_image'
        if ($request->hasFile('eventPhoto')) {
            $image = $request->file('eventPhoto');
            $imagePath = $image->storeAs($eventDirectory . '/main_image', time().'_'.$image->getClientOriginalName(), 'public');
            $event->main_image = $imagePath;
            Log::info('Imagen principal del evento almacenada: ' . $imagePath);
        }

    
        $event->name = $eventName;
        $event->description = $eventDesc;
        $event->video_link = $eventVid;
        $event->venue_id = $venue->id;
        $event->hidden = $eventHidden;

        Log::info('Actualizando evento: ', ['event' => $event]);
    
        try {
            $event->save();
            Log::info('Evento guardado exitosamente.');
            // Agregar mensaje flash a la sesión
            Session::flash('success_message', 'Event: ' . $eventName . ' editat amb èxit');
            return response()->json(['event' => $event]);
        } catch (\Exception $e) {
            Log::error('Error al guardar el evento: ', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Error al guardar el evento'], 500);
        }
    }    
}
