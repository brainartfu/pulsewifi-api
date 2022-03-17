<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'username' => "SuperAdmin",
            'firstname' => "Kumar",
            'lastname' => "Mrinal",
            'email' => 'pulsesuper@gmail.com',
            'role' => 1,
            'enabled' => 1,
            'password' => Hash::make('superadmin987'),
            'email_verified' => 1,
            'lead_process' => 2,
            'address' => 'Kamalpur',
            'city' => 'Mongalkote',
            'state' => 'West Bengal',
            'postal_code' => 713147,
            'country' => 'India',
        ]);

    }
}