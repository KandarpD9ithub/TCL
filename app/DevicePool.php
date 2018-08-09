<?php

/**
 * @package App
 *
 * @class DevicePool
 *
 * @author Parth Patel <parth.d9ithub@gmail.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class DevicePool extends Model
{
     protected $fillable = array('device_type_id', 'original_UUID','status','created_at','updated_at');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    // since the plural of fish isnt what we named our database table we have to define it
    protected $table = 'device_pool';
}
