<?php
/**
 * @package App
 *
 * @class DiscountOfferRule
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscountOfferRule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'to_date', 'from_date', 'rule_type', 'amount_type', 'conditions', 'amount', 'is_active',
        'discount_qty_step'
    ];
}
