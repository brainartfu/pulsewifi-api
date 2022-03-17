<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 255)->nullable(false);
            $table->string('last_name', 255)->nullable(false);
            $table->string('phone', 20)->nullable(false);
            $table->string('active_package', 255)->nullable(false);
            $table->string('package_status', 20)->nullable(false);
            $table->string('data_consume', 255)->nullable(false);
            $table->string('duration', 255)->nullable(false);
            $table->string('connected_devices', 255)->nullable(false);
            $table->string('address', 255)->nullable(false);
            $table->string('city', 255)->nullable(false);
            $table->string('state', 100)->nullable(false);
            $table->string('country', 255)->nullable(false);
            $table->integer('postal_code')->nullable(false);
            $table->string('expired_at', 255)->nullable(false);
            $table->string('last_recharged', 255)->nullable(false);
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
        Schema::dropIfExists('subscribers');
    }
}
