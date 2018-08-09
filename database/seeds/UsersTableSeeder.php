<?php
/**
 * @package Database/seeds
 *
 * @class UsersTableSeeder
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            'name'      => 'admin',
            'email'     => 'admin@example.org',
            'password'  => bcrypt('Admin@1234'),
            'role_name' => '1'
        ]);
    }
}
