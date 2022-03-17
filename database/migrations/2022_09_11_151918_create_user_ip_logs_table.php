<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserIpLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_ip_logs', function (Blueprint $table) {
            $table->id();
            $table->string('src_ip', 255)->nullable(false);
            $table->string('dest_ip', 255)->nullable(false);
            $table->string('protocol', 255)->nullable(false);
            $table->string('port', 255)->nullable(false)->default('443');
            $table->string('username', 255)->nullable(false);
            $table->string('src_port', 255)->nullable(true);
            $table->string('dest_port', 255)->nullable(true);
            $table->string('client_device_ip', 255)->nullable(true);
            $table->string('client_device_ip_type', 255)->nullable(true);
            $table->string('client_device_translated_ip', 255)->nullable(true);
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
        Schema::dropIfExists('user_ip_logs');
    }
}
