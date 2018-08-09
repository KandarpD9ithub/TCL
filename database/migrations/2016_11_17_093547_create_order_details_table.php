<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('order_id');
            $table->double('quantity', 10,2);
            $table->unsignedTinyInteger('is_product_variant')->defaule(0);
            $table->unsignedInteger('removed_by')->nullable();
            $table->string('remove_reason')->nullable();
            $table->string('sub_total', 30)->nullable();
            $table->string('discount', 30)->nullable();
            $table->string('offer', 30)->nullable();
            $table->string('tax_collected', 30)->nullable();
            $table->string('grand_total', 30)->nullable();
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
        Schema::dropIfExists('order_details');
    }
}
