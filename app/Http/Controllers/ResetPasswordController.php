<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        // Muestra el formulario para restablecer la contraseña
        return view('auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        // Validar la información del formulario
        $request->validate([
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

        // Verificación de caducidad del token
        $tokenData = DB::table('password_reset_tokens')
                       ->where('email', $request->email)
                       ->where('token', $request->token)
                       ->first();

        // Obtiene la duración de caducidad desde .env, o usa un valor predeterminado
        $tokenLifetime = env('PASSWORD_RESET_EXPIRATION', 60);

        // El token no existe o ha caducado
        if (!$tokenData || Carbon::parse($tokenData->created_at)->addMinutes($tokenLifetime)->isPast()) {
            return back()->withErrors(['email' => 'El enlace de restablecimiento de contraseña ha caducado o es inválido.']);
        }

        // Intentar restablecer la contraseña
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

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}

