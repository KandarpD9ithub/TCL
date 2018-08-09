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

class CreateProductPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->string('file_name', 100)->nullable();
            $table->string('original_file_name', 255)->nullable();
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
        Schema::dropIfExists('product_photos');
    }
}
