<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use Illuminate\Http\Request;

class DeviceRegister
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->uid) {
            // Get client token by client_token
            $client_token = Cache::get("client:uid_{$request->uid}");

            if ($client_token) {
                return response()->json(['result' => true, 'message' => 'Register OK', 'client-token' => $client_token], 200);
            }

            return $next($request);
        }
        // Return bad request message
        return response()->json(['result' => false, 'message' => 'Client uid cannot be null!'], 400);
    }
}
