<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NetworkSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('network_setting')->insert([
            'guestEssid' => "SuperAdmin",
            'freeWiFi' => 1,
            'freeBandwidth' => 150,
            'freeDailySession' => 60,
            'freeDataLimit' => 500,
            'serverWhitelist' => 'google.com,login.cnctdwifi.com,www.yahoo.com',
            'domainWhitelist' => '.google.com,.cnctdwifi.com,.yahoo.com',
        ]);

    }
}