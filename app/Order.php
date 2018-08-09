<?php
/**
 * @package App
 *
 * @class Order
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'order_number', 'status', 'payment_method', 'order_taken_by', 'progress', 'delivered_at', 'cancelled_by', 'cancel_reason', 'total_amount', 'transaction_id', 'cash_given', 'created_at', 'sub_total', 'discount', 'offer', 'tax_collected', 'grand_total', 'ordered_at', 'in_progress_by', 'ready_by', 'delivered_by',
        'table_id', 'ordered_at', 'ready_at', 'paytm_mobile', 'card_number'
    ];
    public $timestamps = false;
    /**
     * @return string
     */
    public function getNewOrderNumber()
    {
        $data = \DB::table('orders')
            ->select('order_number')
            ->orderBy('id', 'desc')
            ->take(1)
            ->get();

        if (count($data) == 0) {
            $number = 'Order-1001';
        } else {
            $number = abs(filter_var($data[0]->order_number, FILTER_SANITIZE_NUMBER_INT));
            $number = 'Order-' . ++$number;
        }
        return $number;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orderDetail()
    {
        return $this->hasMany('App\OrderDetail');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(\App\Customer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->hasOne('App\Employee', 'user_id', 'order_taken_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ncOrder()
    {
        return $this->hasOne('App\NcOrder');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manageTable()
    {
        return $this->hasOne(\App\ManageTable::class, 'id', 'table_id');
    }
}
