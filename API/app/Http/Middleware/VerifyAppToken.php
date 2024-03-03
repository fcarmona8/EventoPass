<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAppToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('APP-TOKEN');

        if ($token !== config('app.tiquets_app_token')) {
            return response()->json(['message' => 'Acceso no autorizado.'], 403);
        }

        return $next($request);
    }
}
