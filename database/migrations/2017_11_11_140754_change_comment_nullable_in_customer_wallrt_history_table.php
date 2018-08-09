<?php
/**
 * @package Database/migrations
 *
 * @class ChangeCommentNullableInCustomerWallrtHistoryTable
 *
 * @author Parth Patel <parth.d9ithub@gmail.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCommentNullableInCustomerWallrtHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_wallet_history', function (Blueprint $table) {
            $table->string('comment')->nullable()->change();
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
