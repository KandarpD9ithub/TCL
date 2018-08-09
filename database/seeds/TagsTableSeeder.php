<?php

/**
 * @package Database/seeds
 *
 * @class TagsTableSeeder
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('tags')->truncate();
        \DB::table('tags')->insert([
            [
                'id'        => 1,
                'name'      => 'Veg',
                'tag_icon_image'=> "vegetarian-food-symbol.png",
                'is_active'  => 1,
            ], [
                'id'        => 2,
                'name'      => 'Non-Veg',
                'tag_icon_image'=> "non-vegetarian-food-symbol.png",
                'is_active'  => 1,
            ],
            [
                'id'        => 3,
                'name'      => 'Alcoholic',
                'tag_icon_image'=> "beer-glass.png",
                'is_active'  => 1,
            ],
            [
                'id'        => 4,
                'name'      => 'Non-Alcoholic',
                'tag_icon_image'=> "no-alcohol.png",
                'is_active'  => 1,
            ]]);
    }
}
