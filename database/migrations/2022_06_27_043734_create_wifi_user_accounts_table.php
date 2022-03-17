<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWifiUserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wifi_user_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->nullable(false);
            $table->string('first_name', 255)->nullable(false);
            $table->string('last_name', 255)->nullable(false);
            $table->string('email', 255)->nullable(false);
            $table->string('password', 255)->nullable(false);
            $table->tinyInteger('account_verified')->nullable(false)->default(0);
            $table->string('otp_code', 4)->nullable(false);
            $table->tinyInteger('email_verified')->nullable(false)->default(0);
            $table->string('email_verify_code', 4)->nullable(false);
            $table->string('pdoa_id', 255)->nullable(false);
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
        Schema::dropIfExists('wifi_user_accounts');
    }
}
