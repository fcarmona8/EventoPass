<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Session;
use App\Models\Comentario;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class ShowEventController extends Controller
{
    /**
     * Obtiene las coordenadas geográficas de la ubicación de un evento a partir de su dirección.
     * Utiliza el servicio de geocodificación de OpenStreetMap para convertir una dirección en coordenadas latitud/longitud.
     *
     * @param  object $venue
     * @return object|null
     */
    function getCoordinates($venue)
    {
        $start = microtime(true);
        Log::channel('showevent')->info("Iniciando getCoordinates", ['venue' => $venue]);

        $address = urlencode("{$venue->city}, {$venue->province}, {$venue->postal_code}");

        $url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&addressdetails=1&q=$address";

        Log::channel('showevent')->debug("URL de solicitud a OpenStreetMap Nominatim: $url");

        $opts = [
            "http" => [
                "header" => "User-Agent: MyEventApp/1.0"
            ]
        ];
        $context = stream_context_create($opts);

        $response = file_get_contents($url, false, $context);
        $data = json_decode($response);

        Log::channel('showevent')->debug("Respuesta cruda de Nominatim: " . print_r($data, true));

        if (!empty($data)) {
            $duration = microtime(true) - $start;
            Log::channel('showevent')->info("Finalizando getCoordinates", ['duration' => $duration]);
            
            $location = $data[0];
            return (object)['lat' => $location->lat, 'lon' => $location->lon];
        } else {
            Log::channel('showevent')->error("No se encontraron coordenadas para la dirección dada. Respuesta de Nominatim: " . print_r($data, true));
            return null;
        }
    }

    /**
     * Muestra los detalles de un evento específico, incluyendo información sobre el evento, sesiones disponibles,
     * tipos de entradas y comentarios. También calcula y muestra las coordenadas del lugar del evento.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
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
            ->where('closed', false)
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

        $comentarios = $event->obtenerComentarios();

        $coordinates = $this->getCoordinates($event->venue);

        $duration = microtime(true) - $start;
        Log::channel('showevent')->info("Finalizando show", ['duration' => $duration]);

        $metaData = [
            'title' => $event->name . ' - Detalls de l\'Esdeveniment | EventoPass',
            'description' => 'Descobreix tot sobre ' . $event->name . '. Informació detallada, ubicació, dates de sessions i opinions. No et perdis aquest esdeveniment únic!',
            'keywords' => 'EventoPass, esdeveniments, ' . $event->name . ', detalls de l\'esdeveniment, sessions d\'esdeveniment, comentaris d\'esdeveniment',
            'ogType' => 'website',
            'ogUrl' => request()->url(),
            'ogTitle' => $event->name . ' a EventoPass',
            'ogDescription' => 'Explora ' . $event->name . ' a EventoPass. Veure dates de sessions, ubicació i llegir comentaris. Uneix-te a nosaltres per a aquesta experiència inoblidable.',
            'ogImage' => asset('logo/logo.png'),
            'twitterCard' => 'summary_large_image',
            'twitterUrl' => request()->url(),
            'twitterTitle' => $event->name . ' - EventoPass',
            'twitterDescription' => 'Tot el que necessites saber sobre ' . $event->name . '. Dates, ubicació, i comentaris. Reserva la teva entrada ara a EventoPass.',
            'twitterImage' => asset('logo/logo.png'),
        ];

        return view('tickets.showevent', compact('event', 'formattedSessions', 'coordinates', 'comentarios', 'metaData'));
    }
}
