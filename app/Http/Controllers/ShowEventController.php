<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Session;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class ShowEventController extends Controller
{
    function getCoordinates($venue)
    {
        // Inicialización del registro
        Log::info("Iniciando la obtención de coordenadas para: Provincia - {$venue->province}, Ciudad - {$venue->city}, Código Postal - {$venue->postal_code}");
    
        $apiKey = 'AIzaSyCbSv4bCYfNwXa_MXnzon8gG2kK_1MpoZw';
        $address = urlencode("{$venue->province}, {$venue->city}, {$venue->postal_code}");
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=$apiKey";
    
        // Registro de la URL solicitada
        Log::debug("URL de solicitud de la API de Google Maps: $url");
    
        $response = file_get_contents($url);
        $data = json_decode($response);
    
        // Registro de la respuesta cruda de la API
        Log::debug("Respuesta cruda de la API de Google Maps: " . print_r($data, true));
    
        if (isset($data->results[0])) {
            // Registro de éxito
            Log::info("Coordenadas encontradas: " . print_r($data->results[0]->geometry->location, true));
            return $data->results[0]->geometry->location;
        } else {
            // Registro de error
            Log::error("No se encontraron coordenadas para la dirección dada. Respuesta de la API: " . print_r($data, true));
            return null;
        }
    }    

    public function show($id)
    {
        $event = Event::with('images', 'venue')->find($id);

        if (!$event) {
            return redirect()->route('home')->with('error', 'Evento no encontrado.');
        }
        

        $sessions = Session::where('event_id', $event->id)
            ->where('date_time', '>=', now())
            ->orderBy('date_time')
            ->get()
            ->groupBy(function ($session) {
                return Carbon::parse($session->date_time)->format('Y-m-d');
            });

        $formattedSessions = [];
        foreach ($sessions as $date => $sessionsOnDate) {
            $formattedSessions[] = [
                'date' => $date,
                'count' => count($sessionsOnDate),
                'sessions' => $sessionsOnDate->map(function ($session) {
                    $ticketTypeIds = Ticket::where('session_id', $session->id)->pluck('type_id');
                    $session->ticketTypes = TicketType::whereIn('id', $ticketTypeIds)->get();
                    $session->formattedDateTime = Carbon::parse($session->date_time)->format('Y-m-d H:i');
                    return $session;
                }),
            ];
        }

        $coordinates = $this->getCoordinates($event->venue);
        return view('tickets.showevent', compact('event', 'formattedSessions', 'coordinates'));
    }
}
