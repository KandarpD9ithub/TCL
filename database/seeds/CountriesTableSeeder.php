<?php
/**
 * @package Database/seeds
 *
 * @class CountriesTableSeeder
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->delete();
        $countries = [
            [
                'id' => '1',
                'name' => 'India',
                'country_code' => 'IN',
                'capital' => 'New Delhi',
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            ],
        ];
        DB::table('countries')->insert($countries);
    }
}
