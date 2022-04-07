<?php

namespace App\Http\Controllers\ClientAPI;

use App\Models\Device;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Cache;

class DeviceController extends Controller
{
    public function register(Request $request, $uid)
    {
        $device = Device::where('uid', $uid)->first();

        if ($device) {
            $client_token = md5($uid);
            // Set client token to redis cache - Could be array with more device info
            // Cache time can be set as desired
            Cache::put("client_token:{$uid}", $client_token);
            return response()->json(['result' => true, 'message' => 'Register OK', 'client-token' => $client_token], 200);
        } else {
            if ($request->appId && $request->language && $request->os) {
                $newDevice = new Device;
                $newDevice->uid = $uid;
                $newDevice->appId = $request->appId;
                $newDevice->language = $request->language;
                $newDevice->os = $request->os;
                if ($newDevice->save()) {
                    $client_token = md5($uid);
                    // Set client token to redis cache - Could be array with more device info
                    // Cache time can be set as desired
                    Cache::put("client_token:{$uid}", $client_token);
                    return response()->json(['result' => true, 'message' => 'Register OK', 'client-token' => $client_token], 200);
                }
            }
            // Return bad request message
            return response()->json(['result' => false, 'message' => 'Device fields cannot be null!'], 400);
        }
        return response()->json(['result' => true, 'message' => 'Register OK', 'client-token' => $client_token], 200);
    }
}
