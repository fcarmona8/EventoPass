<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        Log::channel('logout')->info('Inicio del proceso de cierre de sesión', ['user_id' => Auth::id()]);

        try {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::channel('logout')->info('Sesión cerrada con éxito', ['user_id' => Auth::id()]);

            // Redireccionar a la página Home, que redirigirá a Login si el usuario no está autenticado
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
