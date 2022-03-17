<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressToWifiuserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wifi_user_accounts', function (Blueprint $table) {
            $table->string('state', 100)->nullable(true);
            $table->string('district', 255)->nullable(true);
            $table->integer('pin_code')->nullable(true);
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
            $table->dropColumn("state");
            $table->dropColumn("district");
            $table->dropColumn("pin_code");
        });
    }
}
