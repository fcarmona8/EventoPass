<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Session;
use Illuminate\Http\Request;

class TicketValidateController extends Controller
{
    /**
     * Maneja el proceso de login verificando el código de sesión.
     * Si la sesión con el código proporcionado está cerrada, permite el login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate(['session_code' => 'required|string']);
        
        $session = Session::where('session_code', $request->session_code)->where('closed', true)->first();

        if (!$session) {
            return response()->json([
                'success' => false, 
                'message' => 'Código de sesión inválido o la sesión no está cerrada para nuevos logins.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso.',
            'session_code' => $session->session_code,
        ], 200);
    }

    /**
     * Obtiene la información de un ticket basado en el ID de sesión y un hash único.
     * Valida si el ticket aún no ha sido validado y, de ser así, lo marca como validado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $sessionId Identificador de la sesión.
     * @param  string  $hash Hash único del ticket.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTicketInfo(Request $request, $sessionId, $hash)
    {
        $loggedSessionCode = $request->header('Session-Code');
        
        $session = Session::find($sessionId);

        if (!$session || $session->session_code !== $loggedSessionCode) {
            return response()->json(['success' => false, 'message' => 'La sesión no coincide o no se encontró.'], 403);
        }

        $ticket = Ticket::where('unicIdTicket', $hash)
                        ->where('session_id', $sessionId)
                        ->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket no encontrado.'], 404);
        }

        if ($ticket->is_validated) {
            return response()->json(['success' => false, 'message' => 'El ticket ya ha sido validado.'], 409);
        }

        $ticket->is_validated = true;
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Ticket validado correctamente.',
            'ticket_info' => [
                'name' => $ticket->name,
                'dni' => $ticket->dni,
                'phone' => $ticket->phone,
            ],
        ]);
    }
}
