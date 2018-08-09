<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InactiveMenuItems extends Model
{

    protected $fillable = [
      'franchise_id', 'menu_id'
    ];
}
