<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForignKeyPayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->dropForeign("payouts_wifi_user_id_foreign");
            $table->dropIndex("payouts_wifi_user_id_index");            
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
            $table->integer('wifi_user_id')->nullable(true)->unsigned()->index()->change();
            $table->foreign('wifi_user_id')->references('id')->on('wi_fi_users')->onDelete('cascade')->onUpdate('cascade')->change();
        });
    }
}
