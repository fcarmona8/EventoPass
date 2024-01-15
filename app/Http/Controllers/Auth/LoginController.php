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
        Log::channel('login')->info('Mostrando formulario de login');

        return view('auth.login');
    }

    // Manejar la solicitud de login
    public function login(Request $request)
    {
        Log::channel('login')->info('Inicio de solicitud de login', ['request_params' => $request->only('email')]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Si las credenciales son correctas, redirigir al usuario a su página correspondiente
            $user = Auth::user();
            Log::channel('login')->info('Login exitoso', ['user_id' => $user->id, 'role' => $user->role->name]);

            if ($user->role->name == 'administrador') {
                return redirect()->route('ruta.admin');
            } elseif ($user->role->name == 'promotor') {
                return redirect()->route('promotorhome');
            }
            return redirect()->intended('/');
        }

        // Si las credenciales son incorrectas, volver al login con un mensaje de error
        Log::channel('login')->warning('Credenciales de login incorrectas', ['email' => $request->email]);

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }
}