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
    public function showResetForm(Request $request, $token = null)
    {
        Log::channel('reset_password')->info('Accediendo a ResetPasswordController@showResetForm', ['request_params' => $request->all()]);

        // // Verificación del token
        // $tokenData = DB::table('password_reset_tokens')
        //                ->where('email', $request->email)
        //                ->where('token', $token)
        //                ->first();

        // if (!$tokenData || Carbon::parse($tokenData->created_at)->addMinutes(env('PASSWORD_RESET_EXPIRATION', 60))->isPast()) {
        //     Log::channel('reset_password')->warning('Token de restablecimiento de contraseña caducado o inválido', ['email' => $request->email, 'token' => $token]);

        //     // Redirigir a la vista de token caducado
        //     return view('auth.passwords.token_expired');
        // }

        return view('auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        Log::channel('reset_password')->info('Inicio de solicitud a ResetPasswordController@reset', ['request_params' => $request->all()]);

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

            Log::channel('reset_password')->info('Estado de restablecimiento de contraseña en ResetPasswordController@reset', ['status' => $status, 'email' => $request->email]);

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