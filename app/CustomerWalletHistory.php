<?php
/**
 * @package App
 *
 * @class CustomerWalletHistory
 *
 * @author Parth Patel <parth.d9ithub@gmail.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerWalletHistory extends Model
{
     protected $fillable = array('customer_device_id', 'credit_amount','debit_amount','comment','payment_mode','card_last_digits','created_at','updated_at');
    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    // since the plural of fish isnt what we named our database table we have to define it
    protected $table = 'customer_wallet_history';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customerDevice()
    {
        return $this->belongsTo('App\CustomerDevices');
    }
}