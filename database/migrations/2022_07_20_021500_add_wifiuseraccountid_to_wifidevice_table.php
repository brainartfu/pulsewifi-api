<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWifiuseraccountidToWifideviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wi_fi_devices', function (Blueprint $table) {
            $table->bigInteger('wifi_user_account_id')->nullable(true)->unsigned()->index();
            $table->foreign('wifi_user_account_id')->references('id')->on('wifi_user_accounts')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wi_fi_devices', function (Blueprint $table) {
            $table->dropColumn('wifi_user_account_id');
        });
    }
}
