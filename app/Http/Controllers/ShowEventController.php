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
        $start = microtime(true);
        Log::channel('showevent')->info("Iniciando getCoordinates", ['venue' => $venue]);
    
        $apiKey = 'AIzaSyCbSv4bCYfNwXa_MXnzon8gG2kK_1MpoZw';
        $address = urlencode("{$venue->province}, {$venue->city}, {$venue->postal_code}");
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=$apiKey";
    
        Log::channel('showevent')->debug("URL de solicitud de la API de Google Maps: $url");
    
        $response = file_get_contents($url);
        $data = json_decode($response);
    
        Log::channel('showevent')->debug("Respuesta cruda de la API de Google Maps: " . print_r($data, true));
    
        if (isset($data->results[0])) {
            $duration = microtime(true) - $start;
            Log::channel('showevent')->info("Finalizando getCoordinates", ['duration' => $duration]);
            return $data->results[0]->geometry->location;
        } else {
            Log::channel('showevent')->error("No se encontraron coordenadas para la dirección dada. Respuesta de la API: " . print_r($data, true));
            return null;
        }
    }    

    public function show($id)
    {
        $start = microtime(true);
        Log::channel('showevent')->info("Iniciando show", ['id' => $id]);

        $event = Event::with('images', 'venue')->find($id);

        if (!$event) {
            Log::channel('showevent')->warning("Evento no encontrado", ['id' => $id]);
            return redirect()->route('home')->with('error', 'Evento no encontrado.');
        }

        Log::channel('showevent')->info("Evento encontrado: " . print_r($event->toArray(), true));

        $sessions = Session::where('event_id', $event->id)
            ->where('date_time', '>=', now())
            ->orderBy('date_time')
            ->get()
            ->groupBy(function ($session) {
                return Carbon::parse($session->date_time)->format('Y-m-d');
            });

        Log::channel('showevent')->info("Sesiones encontradas para el evento: " . print_r($sessions->toArray(), true));

        $formattedSessions = [];
        foreach ($sessions as $date => $sessionsOnDate) {
            $sessionDetails = [
                'date' => $date,
                'count' => count($sessionsOnDate),
                'sessions' => $sessionsOnDate->map(function ($session) {
                    $ticketTypeIds = Ticket::where('session_id', $session->id)->pluck('type_id');
                    $session->ticketTypes = TicketType::whereIn('id', $ticketTypeIds)->get();
                    $session->formattedDateTime = Carbon::parse($session->date_time)->format('Y-m-d H:i');
                    return $session;
                }),
            ];
            $formattedSessions[] = $sessionDetails;

            Log::channel('showevent')->debug("Detalles de la sesión: " . print_r($sessionDetails, true));
        }

        $coordinates = $this->getCoordinates($event->venue);

        $duration = microtime(true) - $start;
        Log::channel('showevent')->info("Finalizando show", ['duration' => $duration]);

        return view('tickets.showevent', compact('event', 'formattedSessions', 'coordinates'));
    }
}
