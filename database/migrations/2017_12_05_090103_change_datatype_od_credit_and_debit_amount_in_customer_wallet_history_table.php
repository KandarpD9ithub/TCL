<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDatatypeOdCreditAndDebitAmountInCustomerWalletHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_wallet_history', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `customer_wallet_history` MODIFY `credit_amount` DOUBLE(10,2)  NULL');
             \DB::statement('ALTER TABLE `customer_wallet_history` MODIFY `debit_amount` DOUBLE(10,2)  NULL');
             //$table->double('credit_amount', 10,2)->change()->nullable();
             //$table->double('debit_amount', 10,2)->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_wallet_history', function (Blueprint $table) {
            $table->integer('credit_amount')->change()->nullable();
            $table->integer('debit_amount')->change()->nullable();
        });
    }
}
