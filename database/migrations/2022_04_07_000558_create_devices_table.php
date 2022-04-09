<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// PSR2 NameSpace warning
class CreateDevicesTable extends Migration
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
            $table->uuid('appId');
            $table->uuid('client_token')->unique();
            $table->string('name', 50)->nullable();
            // Default value set to en. Could be change
            $table->string('language')->default('en');
            // Field type could be integer depending by os type. Ex. 0: Android, 1: IOS etc.
            $table->string('os');
            $table->timestamps();

            $table->index(['uid', 'appId', 'client_token'], 'devices_index');
            $table->foreign('appId')->references('guid')->on('applications')->onDelete('cascade');
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
