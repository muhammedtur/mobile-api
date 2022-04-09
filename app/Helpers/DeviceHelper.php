<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

use App\Models\Device;
use App\Models\Subscription;


use Log;

class DeviceHelper
{
    private function __construct()
    {
    }

    public static function purchase($receipt, $device)
    {
        try {
            $API_URL = env("{$device->os}_API_URL");

            if ($API_URL) {
                $URL = Str::replace(':receipt', $receipt, $API_URL);

                $response = Http::acceptJson()->withBasicAuth('test', 'test')->post($URL);
                $response_body = $response->getBody();

                if (is_null($response_body) || empty($response_body)) {
                    return false;
                } else {
                    $data = json_decode($response_body, true);

                    if (!is_array($data)) {
                        return false;
                    }

                    if ($data['status'] && $data['expire_date']) {
                        $subscription = new Subscription;
                        $subscription->device_uid = $device->uid;
                        $subscription->appId = $device->appId;
                        $subscription->os = $device->os;
                        $subscription->expire_date = Carbon::createFromFormat('Y-m-d H:i:s', $data['expire_date']);
                        if ($subscription->save()) {
                            return true;
                        }
                    }
                    return false;
                }
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
