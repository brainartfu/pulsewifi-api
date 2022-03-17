<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id')->nullable(false)->primary();
            $table->integer('owner_id')->nullable(true)->unsigned()->index();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('model_ids',255)->nullable(true);
            $table->string('fee_description', 255)->nullable(true);
            $table->bigInteger('fee_amount')->nullable(true);
            $table->bigInteger('total_amount')->nullable(true);
            $table->string('details', 255)->nullable(true);
            $table->string('processed', 255)->nullable(true);
            $table->string('non_processed', 255)->nullable(true);
            $table->integer('status')->nullable(false);
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
        Schema::dropIfExists('orders');
    }
}