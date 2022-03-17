<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wifi_user_id')->nullable(false)->unsigned()->index();
            $table->foreign('wifi_user_id')->references('id')->on('wi_fi_users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('wifi_user_phone')->nullable(false)->unsigned()->index();
            $table->float('amount')->nullable(false);
            $table->string('payment_method', 255)->nullable(false);
            $table->integer('location_id')->nullable(false)->unsigned()->index();
            $table->foreign('location_id')->references('id')->on('location')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('order_id')->nullable(false)->unsigned()->index();
            $table->foreign('order_id')->references('id')->on('payment_orders')->onDelete('cascade')->onUpdate('cascade');;
            $table->integer('payment_status')->nullable(false);
            $table->string('payment_details', 72)->nullable(true);
            $table->string('pdoa_id', 72)->nullable(false)->index();
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('payments');
    }
}