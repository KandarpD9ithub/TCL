<?php
/**
 * @package App
 *
 * @class CustomerDevices
 *
 * @author Parth Patel <parth.d9ithub@gmail.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */


namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDevices extends Model
{

	use SoftDeletes;
    	 // MASS ASSIGNMENT -------------------------------------------------------
    // define which attributes are mass assignable (for security)
    // we only want these 3 attributes able to be filled
    protected $fillable = array('customer_id', 'device_pool_id','UUID','pin','issued_at','is_active','comments');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    // since the plural of fish isnt what we named our database table we have to define it
    protected $table = 'customer_device';

    protected $dates = ['deleted_at'];
    public function customer() {
    	return $this->belongsTo('App\Customer');
    }
}
