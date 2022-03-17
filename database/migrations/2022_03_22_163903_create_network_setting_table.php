<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworkSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pdoa_id', 72)->nullable(false)->index();
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('guestEssid', 32)->nullable(false);
            $table->integer('freeWiFi')->nullable(false)->default(0);
            $table->integer('freeBandwidth')->nullable(false);
            $table->integer('freeDailySession')->nullable(false);
            $table->integer('freeDataLimit')->nullable(false);
            $table->text('serverWhitelist')->nullable(true);
            $table->text('domainWhitelist')->nullable(true);
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
        Schema::dropIfExists('network_setting');
    }
}