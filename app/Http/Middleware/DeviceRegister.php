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
        $uid = $request->route('uid');

        if ($uid) {
            $client_token = Cache::get("client_token:{$uid}");

            if ($client_token) {
                return response()->json(['result' => true, 'message' => 'Register OK', 'client-token' => $client_token], 200);
            }

            return $next($request);
        }
        // Return bad request message
        return response()->json(['result' => false, 'message' => 'Device id cannot be null!'], 400);
    }
}
