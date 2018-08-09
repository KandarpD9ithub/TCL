<?php

/**
 * @package App
 *
 * @class NcOrder
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class NcOrder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'non_chargeable_people_id', 'comment'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nonChargeablePeople()
    {
        return $this->belongsTo(\App\NonChargeablePeople::class);
    }
}
