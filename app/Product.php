<?php

/**
 * @package App
 *
 * @class Product
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'price', 'description', 'is_active', 'product_code', 'effective_from', 'effective_to','tax_id'
    ];

    /**
     * @return string
     */
    public function getNewProductCode()
    {
        $data = \DB::table('products')
            ->select('product_code')
            ->orderBy('id', 'desc')
            ->take(1)
            ->get();

        if (count($data) == 0) {
            $number = 'SP0001';
        } else {
            $number = ++$data[0]->product_code; // new rfq number increment by 1
        }
        return $number;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function menu()
    {
        return $this->hasOne(\App\Menu::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(\App\Category::class, 'menu', 'product_id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function productPrice()
    {
        return $this->hasOne(\App\ProductPrice::class, 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tax()
    {
        return $this->belongsTo(\App\Tax::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productTag()
    {
        return $this->hasMany(\App\ProductTag::class);
    }
}
