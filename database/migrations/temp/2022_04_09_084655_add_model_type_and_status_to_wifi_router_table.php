<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModelTypeAndStatusToWifiRouterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wifi_router', function (Blueprint $table) {
            $table->integer('model_id')->nullable(false)->unsigned()->index();
            $table->foreign('model_id')->references('id')->on('wifi_router_model')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('status')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wifi_router', function (Blueprint $table) {
            $table->dropForeign(['model_id']); 
            $table->dropColumn('model_id');
            $table->dropColumn('status');
        });
    }
}