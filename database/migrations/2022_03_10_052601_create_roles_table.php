<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable(false);
            $table->string('display_name', 255)->nullable(false);
            $table->integer('Wifi_Users')->nullable(false)->default(0);
            $table->integer('Wifi_Router')->nullable(false)->default(0);
            $table->integer('Location')->nullable(false)->default(0);
            $table->integer('Distributor')->nullable(false)->default(0);
            $table->integer('Franchise')->nullable(false)->default(0);
            $table->integer('Internet_Plan_Setting')->nullable(false)->default(0);
            $table->integer('Internet_Plan_View')->nullable(false)->default(0);
            $table->integer('Payout_Setting')->nullable(false)->default(0);
            $table->integer('Payout_Log')->nullable(false)->default(0);
            $table->integer('Payment_Setting')->nullable(false)->default(0);
            $table->integer('Payment_Log')->nullable(false)->default(0);
            $table->integer('Payout_Log_Process')->nullable(false)->default(0);
            $table->integer('Leads')->nullable(false)->default(0);
            $table->integer('Add_Leads')->nullable(false)->default(0);
            $table->integer('SMS_Gateway')->nullable(false)->default(0);
            $table->integer('SMS_Template')->nullable(false)->default(0);
            $table->integer('Mail_Server')->nullable(false)->default(0);
            $table->integer('Email_Template')->nullable(false)->default(0);
            $table->integer('Network_Setting')->nullable(false)->default(0);
            $table->integer('role_management')->nullable(false)->default(0);
            $table->integer('Add_PDOA')->nullable(false)->default(0);
            $table->integer('PDOA_Management')->nullable(false)->default(0);
            $table->integer('PDOA_Plan')->nullable(false)->default(0);
            $table->integer('Stuff_Management')->nullable(false)->default(0);
            $table->integer('WiFi_Router_Models')->nullable(false)->default(0);
            $table->integer('Products')->nullable(false)->default(1);
            $table->integer('Product_Management')->nullable(false)->default(0);
            $table->integer('Process_Product')->nullable(false)->default(0);
            $table->integer('Cart')->nullable(false)->default(0);
            $table->integer('required')->nullable(false)->default(0);
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
        Schema::dropIfExists('roles');
    }
}