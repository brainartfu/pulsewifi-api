<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('internet_plan_id')->nullable(false)->unsigned()->index();
            $table->foreign('internet_plan_id')->references('id')->on('internet_plans')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('wifi_user_id')->nullable(false)->unsigned()->index();
            $table->foreign('wifi_user_id')->references('id')->on('wi_fi_users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('franchise_id')->nullable(false)->unsigned()->index();
            $table->foreign('franchise_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('status')->nullable(false);
            $table->string('pdoa_id', 72)->nullable(false)->index();
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
            $table->float('amount')->nullable(false);
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
        Schema::dropIfExists('payment_orders');
    }
}