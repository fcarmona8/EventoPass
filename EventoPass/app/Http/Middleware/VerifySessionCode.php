<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use App\Models\Session;
use Illuminate\Http\Request;

class VerifySessionCode
{
    public function handle(Request $request, Closure $next)
    {
        Log::debug('Session-Code Header:', ['Session-Code' => $request->header('Session-Code')]);

        if ($request->path() !== 'api/V1/login') {
            $sessionCode = $request->header('Session-Code');

            if (!$sessionCode) {
                return response()->json(['message' => 'Session code is required.'], 401);
            }

            $session = Session::where('session_code', $sessionCode)->where('closed', true)->first();

            if (!$session) {
                return response()->json(['message' => 'Invalid session code or session closed.'], 401);
            }

            $request->attributes->add(['session_data' => $session]);
        }

        return $next($request);
    }

}
