<?php

/**
 * @package Database/migrations
 *
 * @class AddTaxTypeInTaxesTable
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaxTypeInTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->unsignedTinyInteger('tax_type')->after('tax_rate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxes', function (Blueprint $table) {
            //
        });
    }
}
