<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Session;
use Illuminate\Http\Request;

class TicketValidateController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(['session_code' => 'required|string']);
        
        $session = Session::where('session_code', $request->session_code)->where('closed', true)->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Código de sesión inválido o sesión no está cerrada para nuevos logins.'], 401);
        }

        $request->session()->put('session_code', $session->session_code);

        return response()->json(['success' => true, 'message' => 'Login exitoso.', 'session_id' => $session->id], 200);
    }

    public function getTicketInfo(Request $request, $hash)
    {
        $sessionCode = $request->session()->get('session_code');
        $session = Session::where('session_code', $sessionCode)->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'No se ha encontrado la sesión o el usuario no está logueado.'], 403);
        }

        $ticket = Ticket::with(['session.event', 'purchase'])
                        ->where('unicIdTicket', $hash)
                        ->whereHas('session', function($query) use ($session) {
                            $query->where('id', $session->id);
                        })
                        ->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket no encontrado o no pertenece a la sesión actual.'], 404);
        }

        $isNominal = $ticket->session && $ticket->session->event ? $ticket->session->event->nominal : false;

        if (!$ticket->is_validated) {
            $ticket->is_validated = true;
            $ticket->save();

            $response = [
                'success' => true,
                'message' => 'Ticket validado correctamente.',
                'is_nominal' => $isNominal,
            ];

            if ($isNominal) {
                $response['ticket_info'] = [
                    'name' => $ticket->name ?? 'N/A',
                    'dni' => $ticket->dni ?? 'N/A',
                    'phone' => $ticket->telefono ?? 'N/A',
                    'buyerName' => $ticket->buyerName ?? 'N/A',
                ];
            }

            return response()->json($response);
        } else {
            return response()->json(['success' => false, 'message' => 'El ticket ya ha sido validado.'], 409);
        }
    }
}
