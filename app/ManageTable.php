<?php
/**
 * @package App
 *
 * @class ManageTable
 *
 * @author Parth Patel <parth.d9ithub@gmail.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageTable extends Model
{

	use SoftDeletes;
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'is_active','franchise_id','created_by','updated_by'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
