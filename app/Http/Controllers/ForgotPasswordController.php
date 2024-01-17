<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        Log::channel('forgot_password')->info('Accediendo a ForgotPasswordController@showLinkRequestForm');
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $start = microtime(true);
        Log::channel('forgot_password')->info('Inicio de solicitud a ForgotPasswordController@sendResetLinkEmail');

        try {
            $validatedData = $request->validate(['email' => 'required|email']);

            $status = Password::sendResetLink($validatedData);
            Log::channel('forgot_password')->info('Estado del enlace de restablecimiento de contraseña enviado', ['status' => $status, 'email' => $validatedData['email']]);

            $duration = microtime(true) - $start;
            Log::channel('forgot_password')->info('Fin de solicitud a ForgotPasswordController@sendResetLinkEmail', ['duration' => $duration]);

            if ($status === Password::RESET_LINK_SENT) {
                return back()->with(['status' => __($status)]);
            } else {
                return back()->withErrors(['email' => __($status)]);
            }
        } catch (\Exception $e) {
            Log::channel('forgot_password')->error('Error en ForgotPasswordController@sendResetLinkEmail', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['email' => __('Ha ocurrido un error inesperado.')]);
        }
    }
}
