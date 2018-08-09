<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecialProduct extends Model
{
    protected $fillable = [
        'franchise_id', 'product_id'
    ];
}
