<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends Controller
{
    /**
     * Muestra el formulario de restablecimiento de contraseña.
     * Verifica si el token de restablecimiento es válido y no ha expirado.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string|null $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $start = microtime(true);
        Log::channel('reset_password')->info('Accediendo a ResetPasswordController@showResetForm');

        $tokenData = DB::table('password_reset_tokens')
                       ->where('email', $request->email)
                       ->first();

        if (!$tokenData || Carbon::parse($tokenData->created_at)->addMinutes(env('PASSWORD_RESET_EXPIRATION', 60))->isPast()) {
            Log::channel('reset_password')->warning('Token de restablecimiento de contraseña caducado o inválido', ['email' => $request->email, 'token' => $token]);

            return view('auth.passwords.token_expired');
        }

        $duration = microtime(true) - $start;
        Log::channel('reset_password')->info('Token de restablecimiento de contraseña válido', ['duration' => $duration]);

        return view('auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
    }

    /**
     * Procesa la solicitud de restablecimiento de contraseña.
     * Valida los datos proporcionados y, si son correctos, actualiza la contraseña del usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $start = microtime(true);
        Log::channel('reset_password')->info('Inicio de solicitud a ResetPasswordController@reset');

        $validatedData = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ]);

        Log::channel('reset_password')->info('Datos validados en ResetPasswordController@reset', ['validated_data' => $validatedData]);

        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => bcrypt($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            $duration = microtime(true) - $start;
            Log::channel('reset_password')->info('Fin de solicitud a ResetPasswordController@reset', ['status' => $status, 'duration' => $duration]);

            return $status === Password::PASSWORD_RESET
                        ? redirect()->route('login')->with('status', __($status))
                        : back()->withErrors(['email' => [__($status)]]);
        } catch (\Exception $e) {
            Log::channel('reset_password')->error('Error en ResetPasswordController@reset', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_params' => $request->all()
            ]);

            return back()->withErrors(['email' => 'Error durante el proceso de restablecimiento de contraseña.']);
        }
    }
}