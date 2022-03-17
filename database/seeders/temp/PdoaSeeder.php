<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PdoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pdoas')->insert([
            'id' => "pulse_1_" . date("Y-m-d_H-i-s"),
            'brand_name' => "Pulse WiFi",
            'brand_logo' => "",
            'platform_bg' => "",
            'price' => 5000,
            'pdoa_status' => "1",
            'contact_details' => "",
            'plan_name' => "",
            'domain_name' => "console.pulsewifi.net",
            'max_wifi_router_count' => 10,
            'license_plan_Status' => 0,
        ]);
    }
}