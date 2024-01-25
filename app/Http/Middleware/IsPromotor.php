<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsPromotor
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

        // Verificar si el usuario tiene un rol y si es promotor
        $user = Auth::user();
        if (!$user->role || $user->role->name != 'promotor') {
            return redirect('login');
        }

        return $next($request);
    }
}