<?php

namespace App\Http\Controllers\ClientAPI;

use App\Models\Device;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Cache;

class DeviceController extends Controller
{
    public function register(Request $request)
    {
        // Check device has been registered by uid and appId before
        $device = Device::where('uid', $request->uid)->where('appId', $request->appId)->first();

        if ($device) {
            // Cache time can be set as desired
            // Set client token to redis cache by uid - Could be array with more device info
            Cache::put("client:register:{$request->uid}:{$request->appId}", $device->clientToken);
            // Set uid, subscription and subscription expire date to redis cache by client token - Could be array with more device info
            Cache::put("client:register:{$device->clientToken}", array(
                'uid' => $request->uid,
                'appId' => $request->appId,
            ));
            return response()->json(['result' => true, 'message' => 'Register OK', 'client-token' => $device->clientToken], 200);
        } else {
            if ($request->appId && $request->language && $request->os) {
                $newDevice = new Device;
                $newDevice->uid = $request->uid;
                $newDevice->appId = $request->appId;
                $newDevice->clientToken = md5(microtime().$request->uid);
                $newDevice->language = $request->language;
                $newDevice->os = $request->os;
                if ($newDevice->save()) {
                    // Cache time can be set as desired
                    // Set client token to redis cache - Could be array with more device info
                    Cache::put("client:register:{$request->uid}:{$request->appId}", $newDevice->clientToken);
                    // Set uid, subscription and subscription expire date to redis cache by client token - Could be array with more device info
                    Cache::put("client:register:{$newDevice->clientToken}", array(
                        'uid' => $request->uid,
                        'appId' => $request->appId,
                    ));
                    return response()->json(['result' => true, 'message' => 'Register OK', 'client-token' => $newDevice->clientToken], 200);
                }
            }
            // Return bad request message
            return response()->json(['result' => false, 'message' => 'Device fields cannot be null!'], 400);
        }
        return response()->json(['result' => false, 'message' => 'An error occured while registering device!'], 200);
    }

    public function purhcase(Request $request, $client_token)
    {
    }
}
