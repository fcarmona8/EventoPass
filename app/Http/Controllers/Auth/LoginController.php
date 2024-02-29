<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión al usuario.
     * Si el usuario ya está autenticado, lo redirige a su página de inicio basada en su rol.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
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

        $metaData = [
            'title' => 'Iniciar Sessió - EventoPass | Accedeix al Teu Compte',
            'description' => 'Inicia sessió a EventoPass per descobrir i participar en els millors esdeveniments. Gestiona les teves entrades i preferències des del teu compte.',
            'keywords' => 'EventoPass, iniciar sessió, compte d\'usuari, accés usuari, esdeveniments',
            'ogType' => 'website',
            'ogUrl' => request()->url(),
            'ogTitle' => 'Iniciar Sessió a EventoPass',
            'ogDescription' => 'Accedeix al teu compte d\'EventoPass per explorar esdeveniments únics i gestionar les teves entrades fàcilment.',
            'ogImage' => asset('logo/logo.png'),
            'twitterCard' => 'summary_large_image',
            'twitterUrl' => request()->url(),
            'twitterTitle' => 'Iniciar Sessió a EventoPass',
            'twitterDescription' => 'Utilitza el teu compte d\'EventoPass per accedir a funcionalitats exclusives i personalitzar la teva experiència en esdeveniments.',
            'twitterImage' => asset('logo/logo.png'),
        ];

        return view('auth.login', compact('metaData'));
    }

    /**
     * Maneja la solicitud de inicio de sesión.
     * Valida las credenciales proporcionadas y, si son correctas, inicia sesión del usuario y lo redirige según su rol.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $start = microtime(true);
        Log::channel('login')->info('Inicio de solicitud de login', ['email' => $request->email]);

        $credentials = $request->only('email', 'password');

        if (empty($credentials['email']) || empty($credentials['password'])) {
            Log::channel('login')->warning('Credenciales de login incorrectas', ['email' => $request->email]);

            if (empty($credentials['email']) && empty($credentials['password'])) {
                return back()->withErrors(['email' => 'El campo de correo electrónico es obligatorio.', 'password' => 'El campo de contraseña es obligatorio.']);
            } elseif (empty($credentials['email'])) {
                return back()->withErrors(['email' => 'El campo de correo electrónico es obligatorio.']);
            } elseif (empty($credentials['password'])) {
                return back()->withErrors(['password' => 'El campo de contraseña es obligatorio.']);
            }
        }

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