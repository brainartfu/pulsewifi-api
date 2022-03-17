<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPDOAToEachTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('email_template', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('internet_plans', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('location', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('mail_server', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('network_setting', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('payment_setting', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('payout_setting', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('payouts', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('sms_gateway', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('sms_template', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('wi_fi_users', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('wifi_router', function (Blueprint $table) {
            $table->string('pdoa_id')->nullable(true);
            $table->foreign('pdoa_id')->references('id')->on('pdoas')->onDelete('cascade')->onUpdate('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('email_template', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('internet_plans', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('location', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('mail_server', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('network_setting', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('payment_setting', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('payout_setting', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('payouts', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('sms_gateway', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('sms_template', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('wi_fi_users', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
        Schema::table('wifi_router', function (Blueprint $table) {
            $table->dropForeign(['pdoa_id']);
            $table->dropColumn('pdoa_id');
        });
    }
}