<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

use App\Models\Subscription;
use App\Helpers\DeviceHelper;

class CheckSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscription;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $device = $this->subscription->device;

        if ($device) {
            $receipt = Str::random(16);
            $result = DeviceHelper::purchase($receipt, $device);

            if (!isset($result['status'], $result['message'])) {
                return;
            }

            if ($result['status']) {
                $this->subscription->update(['status' => "renewed"]);
            } elseif ($result['message'] !== "rate-limit") {
                $this->subscription->update(['status' => "canceled"]);
            }
        }
    }
}
