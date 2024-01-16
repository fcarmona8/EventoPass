<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Session;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Ticket;

class ShowEventController extends Controller
{
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

        return view('tickets.showevent', compact('event', 'formattedSessions'));
    }
}
