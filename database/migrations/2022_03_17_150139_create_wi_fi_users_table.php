<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWiFiUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wi_fi_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable(false);
            $table->string('phone', 255)->nullable(false);
            $table->string('email', 255)->nullable(false);
            $table->string('password', 255)->nullable(false);
            $table->string('pdoa', 72)->nullable(false)->index();
            $table->foreign('pdoa')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('wi_fi_users');
    }
}