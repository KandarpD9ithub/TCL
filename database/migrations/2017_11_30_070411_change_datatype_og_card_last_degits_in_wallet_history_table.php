<?php
/**
 * @package Database/migrations
 *
 * @class ChangeDatatypeOgCardLastDegitsInWalletHistoryTable
 *
 * @author Parth Patel <parth.d9ithub@gmail.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDatatypeOgCardLastDegitsInWalletHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_wallet_history', function (Blueprint $table) {
            $table->string('card_last_digits')->change()->nullable();
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
            //
        });
    }
}
