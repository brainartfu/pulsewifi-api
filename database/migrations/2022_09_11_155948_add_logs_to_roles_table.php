<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogsToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->integer('User_IP_Logs')->nullable(false)->default(0);
            $table->integer('SMS_Logs')->nullable(false)->default(0);
            $table->integer('Email_Logs')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn("User_IP_Logs");
            $table->dropColumn("SMS_Logs");
            $table->dropColumn("Email_Logs");
        });
    }
}
