<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketValidateController extends Controller
{
    /**
     * Handle the login using session code.
     */
    public function login(Request $request)
    {
        $request->validate([
            'session_code' => 'required|string',
        ]);

        $sessionCode = $request->input('session_code');
        $session = Session::where('session_code', $sessionCode)->first();

        if ($session && !$session->closed) {
            return response()->json(['success' => true, 'message' => 'Login exitoso.', 'session' => $session]);
        }

        return response()->json(['success' => false, 'message' => 'Código de sesión inválido o sesión cerrada.'], 401);
    }

    public function getTicketInfo(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer',
        ]);

        $ticketId = $request->input('ticket_id');

        $ticket = Ticket::with(['session.event', 'purchase'])
                        ->where('id', $ticketId)->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket no encontrado.'], 404);
        }

        $buyerData = $ticket->purchase ? [
            'name' => $ticket->purchase->name,
            'dni' => $ticket->purchase->dni,
            'phone' => $ticket->purchase->phone,
            'email' => $ticket->purchase->email
        ] : null;

        return response()->json([
            'success' => true,
            'ticket_info' => [
                'is_validated' => $ticket->is_validated,
                'buyer_data' => $buyerData,
            ]
        ]);
    }

    public function validateTicket(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer',
        ]);

        $ticketId = $request->input('ticket_id');
        $ticket = Ticket::find($ticketId);

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket no encontrado.'], 404);
        }

        if ($ticket->is_validated) {
            return response()->json(['success' => false, 'message' => 'El ticket ya ha sido validado.'], 409);
        }

        $ticket->is_validated = true;
        $ticket->save();

        return response()->json(['success' => true, 'message' => 'Ticket validado correctamente.']);
    }
}