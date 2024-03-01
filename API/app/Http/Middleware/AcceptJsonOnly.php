<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AcceptJsonOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->is('api/V1/optimized-images/*')) {
            return $next($request);
        }

        if ($request->header('Accept') !== 'application/json') {
            return response()->json(['message' => 'The server only accepts JSON requests.'], 406);
        }

        return $next($request);
    }
}
