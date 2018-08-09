<?php

/**
 * @package Database/migrations
 *
 * @class CreateDiscountOfferRulesTable
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountOfferRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_offer_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->date('to_date')->nullable();
            $table->date('from_date')->nullable();
            $table->enum('rule_type', ['discount', 'offer']);
            $table->enum('amount_type', ['fixed', 'percent', 'buy_x_get_y_free']);
            $table->text('conditions');
            $table->float('amount', 10, 3);
            $table->string('discount_qty_step', 30)->nullable();
            $table->unsignedTinyInteger('is_active')->default(1);
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
        Schema::dropIfExists('discount_offer_rules');
    }
}
