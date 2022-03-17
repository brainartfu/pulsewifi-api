<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxAndRateToPayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->float('tax_amount')->nullable(false);
            $table->float('distributor_amount')->nullable(false);
            $table->float('franchise_amount')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->dropColumn('tax_amount');
            $table->dropColumn('distributor_amount');
            $table->dropColumn('franchise_amount');
        });
    }
}
