<?php

/**
 * @package Database/migrations
 *
 * @class CreateCustomerWalletHistoryTable
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerWalletHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_wallet_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_device_id');
            $table->unsignedInteger('credit_amount')->nullable();
            $table->unsignedInteger('debit_amount')->nullable();
            $table->text('comment');
            $table->unsignedTinyInteger('payment_mode')->comment('1:Cash, 2:Card, 3:PayTM');
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
        Schema::dropIfExists('customer_wallet_history');
    }
}
