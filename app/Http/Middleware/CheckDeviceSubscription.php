<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use Illuminate\Http\Request;

class CheckDeviceSubscription
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
        $client_token = $request->route('client_token');

        if ($client_token) {
            $subscription = Cache::get("client:subscription:{$client_token}");

            if ($subscription) {
                return response()->json(['status' => $subscription['status'], 'expire_date' => $subscription['expire_date']], 200);
            }
            return $next($request);
        }
        // Return bad request message
        return response()->json(['result' => false, 'message' => 'Client token cannot be null!'], 400);
    }
}
