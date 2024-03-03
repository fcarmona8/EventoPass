<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('login');
        }

        // Verificar si el usuario tiene un rol y si es administrador
        $user = Auth::user();
        if (Auth::user() && Auth::user()->role->name == 'administrador') {
            return $next($request);
        }
        return redirect('/');
    }
}
