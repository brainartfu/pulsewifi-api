<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWiFiUserVerifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wi_fi_user_verifies', function (Blueprint $table) {
            $table->id();
            $table->string('user_phone');
            $table->string('otp');
            $table->string('challenge');
            $table->string('usermac');
            $table->string('url_code')->unique();
            $table->string('os');
            $table->integer('location_id');
            $table->integer('group_id');
            $table->string('status');
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
        Schema::dropIfExists('wi_fi_user_verifies');
    }
}
