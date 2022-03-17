<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductsAndstaffAndRouterModelToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->integer('staff_Management')->nullable(false)->default(0);
            $table->integer('WiFi_Router_Models')->nullable(false)->default(0);
            $table->integer('Products')->nullable(false)->default(1);
            $table->integer('Product_Management')->nullable(false)->default(0);
            $table->integer('Process_Product')->nullable(false)->default(0);
            $table->integer('Cart')->nullable(false)->default(0);
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
            $table->dropColumn('staff_Management');
            $table->dropColumn('WiFi_Router_Models');
            $table->dropColumn('Products');
            $table->dropColumn('Product_Management');
            $table->dropColumn('Process_Product');
            $table->dropColumn('Cart');
        });
    }
}