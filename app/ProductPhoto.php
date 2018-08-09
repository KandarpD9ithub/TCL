<?php

namespace App;
/**
 * @package App
 *
 * @class ProductPhotos
 *
 * @author Bhavana <bhavana@surmountsoft.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 * */

use Illuminate\Database\Eloquent\Model;

class ProductPhoto extends Model
{
    protected $fillable = [
        'id', 'product_id', 'file_name', 'original_file_name',
    ];

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

    public function menu()
    {
        return $this->hasOne(\App\Menu::class);
    }

    public function categories()
    {
        return $this->belongsToMany(\App\Category::class, 'menu', 'product_id', 'category_id');
    }

    public function productPrice()
    {
        return $this->hasOne(\App\ProductPrice::class, 'product_id');
    }
}
