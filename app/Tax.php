<?php

/**
 * @package App
 *
 * @class Tax
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tax extends Model
{

    use SoftDeletes;

    protected $table = 'taxes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'franchise_id','tax_name', 'tax_rate', 'tax_description', 'is_active', 'tax_type'
    ];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function franchise()
    {
        return $this->belongsTo('App\Franchise');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function category()
    {
        return $this->belongsToMany(\App\Category::class, 'tax_id');
    }

    public function products()
    {
        return $this->belongsToMany(\App\Product::class, 'menu', 'category_id', 'product_id');
    }
}
