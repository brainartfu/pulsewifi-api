<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('location', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable(false);
            $table->integer('owner_id')->unsigned()->index();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('pdoa_id', 72)->nullable(false)->index();
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('address', 255)->nullable(false);
            $table->string('city', 255)->nullable(false);
            $table->string('state', 100)->nullable(false);
            $table->string('country', 255)->nullable(false);
            $table->integer('postal_code')->nullable(false);
            $table->float('latitude', 255)->nullable(false)->default(0);
            $table->float('longitude', 255)->nullable(false)->default(0);
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
        Schema::dropIfExists('location');
    }
}