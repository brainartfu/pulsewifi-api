<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForignOrderIdToWifiorderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // $table->dropForeign(['order_id']);
            // $table->bigInteger('order_id')->nullable(true)->unsigned()->change();
            // $table->foreign('order_id')->references('id')->on('wi_fi_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->dropForeign(['wifi_user_id']);
            // $table->bigInteger('wifi_user_id')->nullable(true)->unsigned()->change();
            // $table->foreign('wifi_user_id')->references('id')->on('wifi_user_accounts')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            // $table->dropForeign(['order_id']);
            // $table->dropForeign(['wifi_user_id']);
            // $table->integer('order_id')->nullable(false)->change();
            // $table->foreign('order_id')->references('id')->on('payment_orders')->onDelete('cascade')->onUpdate('cascade');      
            // $table->integer('wifi_user_id')->nullable(true)->change();
            // $table->foreign('wifi_user_id')->references('id')->on('wi_fi_users')->onDelete('cascade')->onUpdate('cascade');      
        });
    }
}
