<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWiFiOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wi_fi_orders', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->nullable(false);
            $table->integer('internet_plan_id')->nullable(false);
            $table->integer('amount')->nullable(false);
            $table->integer('franchise_id')->nullable(true);
            $table->integer('status')->nullable(false)->default(0);
            $table->string('payment_reference')->nullable(true);
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
        Schema::dropIfExists('wi_fi_orders');
    }
}
