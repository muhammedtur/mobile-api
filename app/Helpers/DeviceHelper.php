<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
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

    public static function purchase($receipt, $device, $sub_status)
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
                    return array(
                        'status'=> false,
                        'message' => 'Mock API can’t be reached'
                    );
                } else {
                    $data = json_decode($response_body, true);

                    if (!isset($data['status'], $data['message'])) {
                        return array(
                            'status'=> false,
                            'message' => 'Mock API can’t be reached'
                        );
                    }

                    if ($data['status']) {
                        $result = Subscription::updateOrCreate(
                            ['client_token' => $device->client_token],
                            [
                            'expire_date' => Carbon::createFromFormat('Y-m-d H:i:s', $data['expire_date']),
                            'status' => $sub_status ? "renewed" : "started",
                            ]
                        );

                        if ($result) {
                            return array(
                                'status'=> $data['status'],
                                'expire_date' => $data['expire_date'],
                                'message' => $data['message']
                            );
                        }
                    }
                    return array(
                        'status'=> false,
                        'message' => $data['message']
                    );
                }
            }
        } catch (\Exception $e) {
            return array(
                'status'=> false,
                'message' => 'Error occurred while purchasing!'
            );
        }
    }
}
