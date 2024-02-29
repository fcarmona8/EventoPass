<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use League\Csv\Writer;
use App\Models\Session;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PromotorSessionsListController extends Controller
{

    /**
     * Muestra una lista de sesiones para un evento específico o todos los eventos de un promotor.
     * Permite al promotor ver el detalle de las sesiones, incluyendo el número de entradas vendidas.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $event_id = $request->input('id');
        $user_id = Auth::user()->id;

        $sessions = collect();
        $events = null;

        if ($event_id) {
            $sessions = Session::with('event')
                ->where('event_id', $event_id)
                ->orderBy('date_time')
                ->get()
                ->map(function ($session) {
                    $session->sold_tickets = Ticket::where('session_id', $session->id)
                        ->whereNotNull('purchase_id')
                        ->count();
                    return $session;
                });
            $isSpecificEvent = true;
        } else {
            $events = Event::where('user_id', $user_id)
                ->with(['sessions' => function($query) {
                    $query
                    ->where('date_time', '>=', now()
                    ->subDays(30))->orderBy('date_time');
                }])
                ->get()
                ->map(function ($event) {
                    $event->sessions->map(function ($session) {
                        $session->sold_tickets = Ticket::where('session_id', $session->id)
                            ->whereNotNull('purchase_id')
                            ->count();
                        return $session;
                    });
                    return $event;
                });
            $isSpecificEvent = false;
        }

        $primeraSesion = Session::with('event')
            ->where('event_id', $event_id)
            ->orderBy('id')
            ->first();

        $ticketsPrimeraSesion = collect();

        if ($primeraSesion) {

            $ticketsPrimeraSesion = $primeraSesion->tickets;
            $ticketsPrimeraSesion = $primeraSesion->tickets->pluck('type')->unique();

        }

        $metaData = [
            'title' => 'Gestiona Sessions d\'Esdeveniments - EventoPass | Veure i Crear Sessions',
            'description' => 'Accedeix al detall de sessions per als teus esdeveniments a EventoPass. Crea noves sessions per a ampliar l\'oferta d\'esdeveniments als teus assistents.',
            'keywords' => 'EventoPass, sessions d\'esdeveniments, gestió de sessions, crear sessions, esdeveniments',
            'ogType' => 'website',
            'ogUrl' => request()->url(),
            'ogTitle' => 'Gestiona i Crea Sessions per als Teus Esdeveniments a EventoPass',
            'ogDescription' => 'Descobreix com gestionar eficaçment les sessions dels teus esdeveniments a EventoPass. Crea noves sessions per a atraure més assistents.',
            'ogImage' => asset('logo/logo.png'),
            'twitterCard' => 'summary_large_image',
            'twitterUrl' => request()->url(),
            'twitterTitle' => 'Gestiona Sessions d\'Esdeveniments - EventoPass',
            'twitterDescription' => 'Administra les sessions dels teus esdeveniments fàcilment amb EventoPass. Afegeix noves sessions per a oferir més opcions als teus assistents.',
            'twitterImage' => asset('logo/logo.png'),
        ];


        return view('promotor/promotorSessionsList', compact(
            'sessions',
            'events',
            'isSpecificEvent',
            'event_id',
            'primeraSesion',
            'ticketsPrimeraSesion',
            'metaData'
        )
        );
    }

    /**
     * Maneja la creación de una nueva sesión para un evento existente.
     * Valida los datos de entrada y crea la sesión junto con los tipos de entradas especificados.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSession(Request $request)
    {
        try {

            $event_id = $request->input('id');

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

            $existingSessions = Session::with('event')
                ->where('event_id', $event_id)
                ->orderBy('date_time')
                ->get()
                ->map(function ($session) {
                    $session->sold_tickets = Ticket::where('session_id', $session->id)
                        ->whereNotNull('purchase_id')
                        ->count();
                    return $session;
                });

            return redirect()->route('promotorsessionslist', ['id' => $event_id])->with('success', 'Sesión creada con éxito');
        } catch (\Exception $e) {
            Log::error('Error en el proceso de almacenamiento de la sesión: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar la sesión.']);
        }
    }

    /**
     * Genera y descarga un archivo CSV con la lista de asistentes a una sesión específica.
     * Incluye detalles como el nombre del comprador, el nombre del asistente, el código de la entrada y el tipo de entrada.
     *
     * @param  int $id
     */
    public function CSVdownload($id)
    {
        Log::info('Entrando en método CSVdownload de PromotorSessionListController.', ['id' => $id]);

        $session = Session::find($id);

        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        $csv->insertOne(['Nom comprador', 'Nom de l’assistent', 'Codi d’entrada', 'Tipus d’entrada']);
        foreach ($session->tickets as $ticket) {
            $typeTicket = TicketType::find($ticket->type_id)->name;
            if ($ticket->purchase_id !== null) {

                if($ticket->name == null){
                    $csv->insertOne([
                        $ticket->buyerName,
                        "",
                        $ticket->unicIdTicket,
                        $typeTicket
                    ]);
                }else{
                    $csv->insertOne([
                        $ticket->buyerName,
                        $ticket->name,
                        $ticket->unicIdTicket,
                        $typeTicket
                    ]);
                }
            }
        }

        $csv->output('sesion' . $session->id . '.csv');
    }

    /**
     * Permite editar los detalles de una sesión existente, como abrir o cerrar la sesión a nuevas ventas de entradas.
     * Actualiza el estado de la sesión y, si se cierra, genera un código de acceso para la sesión.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id 
     * @return \Illuminate\Http\JsonResponse
     */
    public function editSession(Request $request, $id)
    {
        Log::info('Entrando en método editSession de PromotorSessionListController.', ['id' => $id]);

        $session = Session::findOrFail($id);

        if ($request->has('closed')) {
            $session->closed = $request->closed;

            if ($session->closed) {
                $session->session_code = $this->generateAccessCode();
            } else {
                $session->session_code = null;
            }

            $session->save();

            return response()->json(['success' => true, 'session_code' => $session->session_code]);
        } else {
            return response()->json(['error' => 'El campo "closed" no está presente en la solicitud.'], 400);
        }
    }

    /**
     * Genera un código de acceso único para una sesión cerrada.
     * Utiliza una combinación aleatoria de números y letras mayúsculas.
     *
     * @return string
     */
    private function generateAccessCode()
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $accessCode = '';

        for ($i = 0; $i < 8; $i++) {
            $accessCode .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $accessCode;
    }
        
}