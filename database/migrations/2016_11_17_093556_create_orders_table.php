<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->string('transaction_id')->nullable();
            $table->string('order_number', 100);
            $table->enum('status', ['ordered', 'in_progress', 'ready', 'delivered', 'cancelled'])->default('ordered');
            $table->unsignedTinyInteger('payment_method')->comment('1: cash, 2:card, 3:paytm, 4:wallet');
            $table->integer('cash_given')->nullable();
            $table->unsignedInteger('order_taken_by');
            $table->dateTime('progress')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->unsignedInteger('cancelled_by')->nullable();
            $table->string('cancel_reason', 100)->nullable();
            $table->unsignedInteger('in_progress_by')->nullable();
            $table->unsignedInteger('ready_by')->nullable();
            $table->unsignedInteger('delivered_by')->nullable();
            $table->string('table_no', 45)->nullable();
            $table->dateTime('ordered_at')->nullable();
            $table->dateTime('ready_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
