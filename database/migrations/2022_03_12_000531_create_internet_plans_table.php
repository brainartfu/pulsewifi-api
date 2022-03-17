<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternetPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internet_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable(false);
            $table->string('description', 255)->nullable(true);
            $table->integer('price')->nullable(false);
            $table->integer('validity')->nullable(false);
            $table->integer('bandwidth')->nullable(false);
            $table->integer('data_limit')->nullable(false);
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
        Schema::dropIfExists('internet_plans');
    }
}