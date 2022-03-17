<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserinfosToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name')->nullable(true);
            $table->string('designation')->nullable(true);
            $table->string('id_proof')->nullable(true);
            $table->string('id_proof_no')->nullable(true);
            $table->string('upload_id_proof')->nullable(true);
            $table->string('identity_verification')->nullable(true);
            $table->integer('pin_code')->nullable(true);
            $table->integer('gst_no')->nullable(true);
            $table->integer('revenue_model')->nullable(true);
            $table->integer('gst_no')->nullable(true);
            $table->integer('revenue_sharing_ratio')->nullable(true);
            $table->string('beneficiary_name')->nullable(true);
            $table->string('ifsc_code')->nullable(true);
            $table->string('ac_no')->nullable(true);
            $table->string('passbook_cheque')->nullable(true);
            $table->string('ac_no')->nullable(true);
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
            $table->dropColumn('company_name');
            $table->dropColumn('designation');
            $table->dropColumn('id_proof');
            $table->dropColumn('id_proof_no');
            $table->dropColumn('upload_id_proof');
            $table->dropColumn('identity_verification');
            $table->dropColumn('pin_code');
            $table->dropColumn('revenue_model');
            $table->dropColumn('revenue_sharing_ratio');
            $table->dropColumn('beneficiary_name');
            $table->dropColumn('ifsc_code');
            $table->dropColumn('ac_no');
            $table->dropColumn('passbook_cheque');
            $table->dropColumn('ac_no');
        });
    }
}