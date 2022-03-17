<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForignKeyPayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->bigInteger('wifi_user_id')->nullable(true)->unsigned()->index()->change();
            $table->foreign('wifi_user_id')->references('id')->on('wifi_user_accounts')->onDelete('cascade')->onUpdate('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->dropForeign("payment_orders_wifi_user_id_foreign");
            $table->dropIndex("payment_orders_wifi_user_id_index"); 
        });
    }
}
