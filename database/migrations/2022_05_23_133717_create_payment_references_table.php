<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_references', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('razorpay_order_id');
            $table->string('razorpay_payment_reference')->nullable();
            $table->string('status')->nullable();
            $table->json('razorpay_response')->nullable();
            $table->string('updated_via')->nullable();
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
        Schema::dropIfExists('payment_references');
    }
}
