<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayoutSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payout_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('franchise_id')->nullable(false)->unsigned()->index();
            $table->foreign('franchise_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('franchise_rate')->nullable(false);
            $table->integer('distributor_id')->nullable(true)->unsigned()->index();
            $table->foreign('distributor_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('distributor_rate')->nullable(false);
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
        Schema::dropIfExists('payout_setting');
    }
}