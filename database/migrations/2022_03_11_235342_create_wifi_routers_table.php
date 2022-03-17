<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWifiRoutersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wifi_router', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable(false);
            $table->string('mac_address', 255)->nullable(false);
            $table->integer('config_version')->nullable(true);
            $table->dateTime('last_online')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('location_id')->nullable(true)->unsigned()->index();
            $table->foreign('location_id')->references('id')->on('location')->onDelete('cascade')->onUpdate('cascade');
            $table->string('pdoa_id', 72)->nullable(false)->index();
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('owner_id')->nullable(true)->unsigned()->index();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('model_id')->nullable(false)->unsigned()->index();
            $table->foreign('model_id')->references('id')->on('wifi_router_model')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('status')->nullable(false)->default(0);

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
        Schema::dropIfExists('wifi_router');
    }
}