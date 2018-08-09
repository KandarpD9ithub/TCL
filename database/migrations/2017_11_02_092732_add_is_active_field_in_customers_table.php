<?php
/**
 * @package Database/migrations
 *
 * @class AddIsActiveFieldInCustomersTable
 *
 * @author Parth Patel <parth.d9ithub@gmail.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsActiveFieldInCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_active')->default(1)->after('email');//add is_active after email in customers table
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
}
