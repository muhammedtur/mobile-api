<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Subscription;
use App\Jobs\CheckSubscriptionJob;

use Carbon\Carbon;

class CheckSubscriptionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:check_subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check devices subscriptions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * It will update the records that have passed the expire date and
         * that have not been canceled by bringing 10 records each with queue
         * Chunk size could be changed
        */
        Subscription::where('status', '!=', 'canceled')->whereDate('expire_date', '<=', Carbon::now(-6))->chunk(10, function ($subscriptions) {
            foreach ($subscriptions as $subscription) {
                CheckSubscriptionJob::dispatch($subscription);
            }
        });
    }
}
