<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->where('id', '=', 1)->orWhere('id', '=', 2)->update([
            'User_IP_Logs' => 1,
            'SMS_Logs' => 1,
            'Email_Logs' => 1
        ]);
    }
}
