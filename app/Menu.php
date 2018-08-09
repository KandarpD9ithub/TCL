<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'category_id','product_id','is_active'
    ];

    public function category()
    {
        return $this->belongsToMany(\App\Category::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }

    public function categoryName(){
        return $this->belongsTo(\App\Category::class, 'category_id');
    }
}
