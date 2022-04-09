<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// PSR2 NameSpace warning
class CreateSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('client_token');
            $table->string('status')->default('started');
            // Subscription due date 1 month from now. Could be change
            $table->datetime('expire_date')->default(Carbon::now()->addMonth()->format('Y-m-d H:i:s'));
            $table->timestamps();

            $table->index(['client_token', 'expire_date'], 'subscription_index');
            $table->foreign('client_token')->references('client_token')->on('devices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription');
    }
}
