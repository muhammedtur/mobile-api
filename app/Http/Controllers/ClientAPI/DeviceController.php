<?php

namespace App\Http\Controllers\ClientAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Device;
use App\Models\Subscription;
use App\Models\Application;
use App\Helpers\DeviceHelper;

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
            Cache::put("client:register:{$request->uid}:{$request->appId}", $device->client_token);
            // Set uid, subscription and subscription expire date to redis cache by client token - Could be array with more device info
            Cache::put("client:register:{$device->client_token}", array(
                'uid' => $request->uid,
                'appId' => $request->appId,
            ));
            return response()->json(['result' => true, 'message' => 'Register OK', 'client-token' => $device->client_token], 200);
        } else {
            if ($request->os) {
                $newDevice = new Device;
                $newDevice->uid = $request->uid;
                $newDevice->appId = $request->appId;
                $newDevice->client_token = md5(microtime().$request->uid);
                $newDevice->language = $request->language;
                $newDevice->os = Str::upper($request->os);
                if ($newDevice->save()) {
                    // Cache time can be set as desired
                    // Set client token to redis cache - Could be array with more device info
                    Cache::put("client:register:{$request->uid}:{$request->appId}", $newDevice->client_token);
                    // Set uid, subscription and subscription expire date to redis cache by client token - Could be array with more device info
                    Cache::put("client:register:{$newDevice->client_token}", array(
                        'uid' => $request->uid,
                        'appId' => $request->appId,
                    ));
                    return response()->json(['result' => true, 'message' => 'Register OK', 'client-token' => $newDevice->client_token], 200);
                }
            }
            // Return bad request message
            return response()->json(['result' => false, 'message' => 'Fields cannot be null!'], 400);
        }
        return response()->json(['result' => false, 'message' => 'An error occured while registering device!'], 200);
    }

    public function purchase(Request $request, $client_token, $receipt)
    {
        // Check device has been registered by uid and appId before
        $device = Device::where('client_token', $client_token)->first();

        if (!$device) {
            return response()->json(['result' => false, 'message' => 'Device should register first!'], 200);
        }

        $result = DeviceHelper::purchase($receipt, $device);

        return response()->json(['result' => $result], 200);
    }

    public function mockApi(Request $request, $receipt)
    {
        if (!$receipt) {
            return response()->json(['result' => false, 'message' => 'Receipt cannot be null!'], 400);
        }

        // Could be check authorization - base64_decode($header)
        $header = $request->header('Authorization');

        // Get last character of receipt
        $last_character = substr($receipt, -1);

        // If last character of receipt odd number then return expire_date UTC -6 timezone and status true
        if (intval($last_character) % 2 !== 0) {
            return response()->json(['status' => true, 'message' => "OK", 'expire_date' => Carbon::now(-6)->format('Y-m-d H:i:s')], 200);
        }

        return response()->json(['status' => false], 400);
    }
}
