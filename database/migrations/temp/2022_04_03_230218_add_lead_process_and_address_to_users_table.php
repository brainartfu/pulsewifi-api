<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadProcessAndAddressToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('lead_process')->nullable(false)->default(0);
            $table->string('address', 255)->nullable(false);
            $table->string('city', 255)->nullable(false);
            $table->string('state', 100)->nullable(false);
            $table->string('country', 255)->nullable(false);
            $table->integer('postal_code')->nullable(false);
            $table->float('latitude', 255)->nullable(false)->default(0);
            $table->float('longitude', 255)->nullable(false)->default(0);
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
            $table->dropColumn('lead_process');            
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('country');
            $table->dropColumn('postal_code');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}