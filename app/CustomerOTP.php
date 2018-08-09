<?php
/**
 * @package App
 *
 * @class CustomerOTP
 *
 * @author Parth Patel <parth.d9ithub@gmail.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerOTP extends Model
{
     protected $fillable = array('customer_id', 'OTP','expire_time');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    // since the plural of fish isnt what we named our database table we have to define it
    protected $table = 'customer_otp';

}
