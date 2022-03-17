<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdoaIdToWifiOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wi_fi_orders', function (Blueprint $table) {
            $table->string('pdoa_id', 72)->nullable(true)->index();
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
        Schema::table('wi_fi_orders', function (Blueprint $table) {
            $table->dropColumn('pdoa_id');
        });
    }
}
