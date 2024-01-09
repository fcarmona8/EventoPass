<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Mostrar el formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Manejar la solicitud de login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Si las credenciales son correctas, redirigir al usuario a su página correspondiente
            $user = Auth::user();
            if($user->role->name == 'administrador') {
                return redirect()->route('ruta.admin');
            } else if($user->role->name == 'promotor') {
                return redirect()->route('promotor.promoterhome');
            }
            return redirect()->intended('/');
        }

        // Si las credenciales son incorrectas, volver al login con un mensaje de error
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }
}

