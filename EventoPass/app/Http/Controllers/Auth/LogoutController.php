<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogoutController extends Controller
{
    /**
     * Cierra la sesión del usuario actual y lo redirige a la página de inicio.
     * Invalida la sesión actual y regenera el token de la sesión para prevenir ataques de tipo session fixation.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $start = microtime(true);
        Log::channel('logout')->info('Inicio del proceso de cierre de sesión', ['user_id' => Auth::id()]);

        try {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $duration = microtime(true) - $start;
            Log::channel('logout')->info('Sesión cerrada con éxito', ['user_id' => Auth::id(), 'duration' => $duration]);

            return redirect('/promotor/promotorhome');
        } catch (\Exception $e) {
            Log::channel('logout')->error('Error al cerrar sesión', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
        }
    }
}
