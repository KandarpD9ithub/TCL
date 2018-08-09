<?php

/**
 * @package Database/migrations
 *
 * @class CreateDevicePoolTable
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicePoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_pool', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('device_type_id');
            $table->string('original_UUID', 60);
            $table->unsignedTinyInteger('status')->comment('1(new), 2(in-use), 3(damaged), 4(lost)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_pool');
    }
}
