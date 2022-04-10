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
        $device = Device::ofApplication($request->appId)->where('uid', $request->uid)->first();

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

        $subscription_check = Subscription::where('client_token', $client_token)->count();
        $result = DeviceHelper::purchase($receipt, $device, $subscription_check);

        return response()->json($result, 200);
    }

    public function checkSubscription(Request $request, $client_token)
    {
        $subscription = Subscription::where('client_token', $client_token)->first();

        if ($subscription) {
            Cache::put("client:subscription:{$client_token}", array(
                'status' => $subscription->status,
                'expire_date' => $subscription->expire_date,
            ));
            return response()->json(['status' => $subscription->status, 'expire_date' => $subscription->expire_date], 400);
        }
        return response()->json(['status' => false, 'message' => 'Subscription not found!'], 400);
    }

    public function mockApi(Request $request, $receipt)
    {
        if (!$receipt) {
            return response()->json(['status' => false, 'message' => 'Receipt cannot be null!'], 400);
        }

        // Could be check authorization - base64_decode($header)
        $header = $request->header('Authorization');

        // Get last character of receipt
        $last_character = substr($receipt, -1);

        // Get last two character of receipt
        $last_two_character = intval(substr($receipt, -2));

        // If last two character of receipt is divisible by 6 then return rate-limit error
        if ($last_two_character != 0 && $last_two_character % 6 === 0) {
            return response()->json(['status' => false, 'message' => "rate-limit"], 400);
        }

        // If last character of receipt odd number then return expire_date (added 1 month from now) UTC -6 timezone and status true
        if (intval($last_character) % 2 !== 0) {
            return response()->json([
                'status' => true,
                'message' => "OK",
                'expire_date' => Carbon::now()->addMonth()->format('Y-m-d H:i:s')
            ], 200);
        }

        return response()->json(['status' => false, 'message' => "Invalid receipt!"], 400);
    }
}
