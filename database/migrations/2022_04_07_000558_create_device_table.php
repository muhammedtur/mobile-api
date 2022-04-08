<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// PSR2 NameSpace warning
class CreateDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uid');
            $table->string('name', 50)->nullable();
            $table->string('appId');
            $table->string('clientToken');
            // Default value set to en. Could be change
            $table->string('language')->default('en');
            // Field type could be integer depending by os type. Ex. 0: Android, 1: IOS etc.
            $table->string('os');
            $table->string('subscription')->default('started');
            // New register. Subscription due date 1 month from now. Could be change
            $table->datetime('subscription_expire_date')->default(Carbon::now()->format('Y-m-d H:i:s')->addMonth());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
