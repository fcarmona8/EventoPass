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
        Log::channel('forgot_password')->info('Inicio de solicitud a ForgotPasswordController@sendResetLinkEmail', ['request_params' => $request->all()]);

        $validatedData = $request->validate(['email' => 'required|email']);
        Log::channel('forgot_password')->info('Datos validados en ForgotPasswordController@sendResetLinkEmail', ['validated_data' => $validatedData]);

        try {
            $status = Password::sendResetLink($validatedData);

            Log::channel('forgot_password')->info('Estado del enlace de restablecimiento de contraseña enviado', ['status' => $status, 'email' => $validatedData['email']]);

            if ($status === Password::RESET_LINK_SENT) {
                $response = back()->with(['status' => __($status)]);
            } else {
                $response = back()->withErrors(['email' => __($status)]);
            }

            Log::channel('forgot_password')->info('Respuesta de ForgotPasswordController@sendResetLinkEmail', ['response_status' => $response->status(), 'response_data' => $response->getSession()->all()]);

            return $response;
        } catch (\Exception $e) {
            Log::channel('forgot_password')->error('Error en ForgotPasswordController@sendResetLinkEmail', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_params' => $request->all()
            ]);

            return back()->withErrors(['email' => __('Ha ocurrido un error inesperado.')]);
        }
    }
}
