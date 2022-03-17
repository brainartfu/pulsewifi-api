<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdoas', function (Blueprint $table) {
            $table->string('id', 72)->nullable(false)->primary();
            $table->string('brand_name', 255)->nullable(false);
            $table->string('brand_logo', 255)->nullable(true);
            $table->string('platform_bg', 255)->nullable(true);
            $table->integer('franchise_fee')->nullable(false);
            $table->integer('distributor_fee')->nullable(false);
            $table->integer('pdoa_status')->nullable(false)->default(0);
            $table->integer('pdoa_plan_id')->unsigned()->index();
            $table->foreign('pdoa_plan_id')->references('id')->on('pdoa_plan')->onDelete('cascade')->onUpdate('cascade');
            $table->string('domain_name', 255)->nullable(false);
            $table->string('username', 255)->nullable(false);
            $table->string('firstname', 255)->nullable(false);
            $table->string('lastname', 255)->nullable(false);
            $table->string('email', 255)->nullable(true);
            $table->string('phone_no', 10)->nullable(true);
            $table->string('cin_no')->nullable(true);
            $table->string('incorporation_cert', 255)->nullable(true);
            $table->string('company_name', 255)->nullable(true);
            $table->string('designation', 255)->nullable(true);
            $table->string('id_proof', 255)->nullable(true);
            $table->string('id_proof_no', 255)->nullable(true);
            $table->string('upload_id_proof')->nullable(true);
            $table->string('identity_verification')->nullable(true);
            $table->string('address', 255)->nullable(true);
            $table->string('city', 255)->nullable(true);
            $table->string('state', 255)->nullable(true);
            $table->string('country', 255)->nullable(true);
            $table->string('postal_code', 255)->nullable(true);
            $table->string('gst_no', 255)->nullable(true);
            $table->integer('payment_status')->nullable(false)->default(0);
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
        Schema::dropIfExists('pdoas');
    }
}