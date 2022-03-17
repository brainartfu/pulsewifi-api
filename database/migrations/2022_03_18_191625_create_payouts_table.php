<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wifi_user_id')->nullable(false)->unsigned()->index();
            $table->foreign('wifi_user_id')->references('id')->on('wifi_user_accounts')->onDelete('cascade')->onUpdate('cascade');
            $table->float('amount')->nullable(false);
            $table->string('payment_method', '24')->nullable(false);
            $table->integer('franchise_id')->nullable(false)->unsigned()->index();
            $table->foreign('franchise_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('distributor_id')->nullable(false)->unsigned()->index();
            $table->foreign('distributor_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('payout_status')->nullable(false);
            $table->string('payout_details', 255)->nullable(true);
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
        Schema::dropIfExists('payouts');
    }
}