<?php

/**
 * @package Database/seeds
 *
 * @class DeviceTypesTableSeeder
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */
use Illuminate\Database\Seeder;

class DeviceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\DeviceType::create([
            'name'      => 'NFC Band',
            'is_active' => 1
        ]);
    }
}
