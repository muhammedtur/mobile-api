<?php

namespace App\Helpers;

use Illuminate\Support\Str;
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
                $username = $device->application->credentials[$device->os]['username'];
                $password = $device->application->credentials[$device->os]['password'];
                $credentials = base64_encode("{$username}:{$password}");

                $response = Http::acceptJson()->withHeaders([
                    'Authorization' => $credentials,
                ])->post($URL);
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
                        $subscription->client_token = $device->client_token;
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
