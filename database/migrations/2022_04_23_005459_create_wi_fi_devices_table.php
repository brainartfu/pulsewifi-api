<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWiFiDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wi_fi_devices', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('otp');
            $table->string('challenge');
            $table->string('usermac');
            $table->string('url_code');
            $table->string('os');
            $table->string('brand');
            $table->integer('location_id');
            $table->integer('pdoa');
            $table->string('status');
            $table->timestamp('otp_generate_time');
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
        Schema::dropIfExists('wi_fi_devices');
    }
}
