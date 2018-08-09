<?php
/**
 * @package App
 *
 * @class Customer
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'contact_number', 'email','profile_picture','address_line_one','address_line_two','city','region','country_id','is_active','comments'
    ];

    public function orders()
    {
        return $this->hasMany(\App\Order::class);
    }

    public function country()
    {
        return $this->belongsTo(\App\Country::class);
    }
}
