<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable(false);
            $table->string('dlt_id', 255)->nullable(true);
            $table->string('sender_id', 255)->nullable(false);
            $table->text('text')->nullable(true);
            $table->integer('status')->nullable(false)->default(0);
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
        Schema::dropIfExists('sms_template');
    }
}