<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeWifiUserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wifi_user_accounts', function (Blueprint $table) {
            $table->string('first_name', 255)->nullable(true)->change();
            $table->string('last_name', 255)->nullable(true)->change();
            $table->string('email', 255)->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wifi_user_accounts', function (Blueprint $table) {
            $table->string('first_name', 255)->nullable(false)->change();
            $table->string('last_name', 255)->nullable(false)->change();
            $table->string('email', 255)->nullable(false)->change();
        });
    }
}
