<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdoaPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdoa_plan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plan_name', 255)->nullable(false);
            $table->float('price')->nullable(false)->default(0);
            $table->integer('max_wifi_router_count')->nullable(false);
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
        Schema::dropIfExists('pdoa_plan');
    }
}