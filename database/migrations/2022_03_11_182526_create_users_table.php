<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('role')->nullable(false)->unsigned()->index();
            $table->foreign('role')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
            $table->string('pdoa_id', 72)->nullable(false)->index();
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('username', 255)->nullable(true);
            $table->string('firstname', 255)->nullable(false);
            $table->string('lastname', 255)->nullable(false);
            $table->string('email', 255)->nullable(false);
            $table->integer('email_verified')->nullable(false)->default(0);
            $table->integer('email_verification_code')->nullable(true);
            $table->string('profile_img_path', 255)->nullable(true);
            $table->string('password', 255)->nullable(false);
            $table->integer('enabled')->nullable(false)->default(0);
            $table->integer('belongs_to')->nullable(true);
            $table->integer('lead_process')->nullable(false)->default(0);
            $table->string('address', 255)->nullable(false);
            $table->string('city', 255)->nullable(false);
            $table->string('state', 100)->nullable(false);
            $table->string('country', 255)->nullable(false);
            $table->integer('postal_code')->nullable(false);
            $table->float('latitude', 255)->nullable(false)->default(0);
            $table->float('longitude', 255)->nullable(false)->default(0);
            $table->string('phone_no', 10)->nullable(true);
            $table->string('company_name', 255)->nullable(true);
            $table->string('designation', 255)->nullable(true);
            $table->string('id_proof', 255)->nullable(true);
            $table->string('id_proof_no', 50)->nullable(true);
            $table->string('upload_id_proof', 255)->nullable(true);
            $table->string('identity_verification', 255)->nullable(true);
            $table->string('gst_no', 50)->nullable(true);
            $table->integer('revenue_model')->nullable(true);
            $table->integer('revenue_sharing_ratio')->nullable(true);
            $table->string('beneficiary_name', 255)->nullable(true);
            $table->string('ifsc_code', 50)->nullable(true);
            $table->string('ac_no', 255)->nullable(true);
            $table->string('passbook_cheque', 255)->nullable(true);
            $table->integer('payment_status')->nullable(true);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}