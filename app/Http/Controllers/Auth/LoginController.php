<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    // Mostrar el formulario de login
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role && $user->role->name == 'promotor') {
                return redirect()->route('promotorhome');
            } else if ($user->role && $user->role->name =='administrador') {
                return redirect()->route('ruta.admin');
            }

            return redirect()->intended('/');
        }

        Log::channel('login')->info('Mostrando formulario de login');
        return view('auth.login');
    }

    // Manejar la solicitud de login
    public function login(Request $request)
    {
        $start = microtime(true);
        Log::channel('login')->info('Inicio de solicitud de login', ['email' => $request->email]);

        $credentials = $request->only('email', 'password');

        try {
            if (Auth::attempt($credentials)) {
            $user = Auth::user();
            Log::channel('login')->info('Login exitoso', ['user_id' => $user->id, 'email' => $user->email]);

            $duration = microtime(true) - $start;
            Log::channel('login')->info('Fin de solicitud de login exitosa', ['duration' => $duration]);

            if ($user->role->name == 'administrador') {
                return redirect()->route('ruta.admin');
            } elseif ($user->role->name == 'promotor') {
                return redirect()->route('promotorhome');
            }
            return redirect()->intended('/');
        }

        Log::channel('login')->warning('Credenciales de login incorrectas', ['email' => $request->email]);

            return back()->withErrors(['email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.']);
        } catch (\Exception $e) {
            Log::channel('login')->error('Error en el proceso de login', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email
            ]);

            return back()->withErrors(['email' => 'Error durante el proceso de login.']);
        }
    }
}