<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->string('id')->nullable(false)->pirmary();
            $table->integer('owner_id')->nullable(true)->unsigned()->index();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('description',255)->nullable(true);
            $table->integer('model_id')->nullable(true)->unsigned()->index();
            $table->foreign('model_id')->references('id')->on('wifi_router_model')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('request_amount')->nullable(true);
            $table->integer('status')->nullable(false);
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
        Schema::dropIfExists('cart');
    }
}