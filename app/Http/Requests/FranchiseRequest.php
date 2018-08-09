<?php
/**
 * @package App\Http\Requests
 *
 * @class FranchiseRequest
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App\Http\Requests;

use App\Franchise;
use Illuminate\Foundation\Http\FormRequest;

class FranchiseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $franchise = Franchise::find($this->franchise);
        $rules =  [
            'name'              => 'required',
            'address_line_one'  => 'required',
            'city'              => 'required',
            'region'            => 'required',
            'country_id'        => 'required',
           /* 'tax.*.rate'        => 'required',*/
            'gst_number'        => 'required|alpha_num|max:15|regex:/^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([0-9a-zA-Z]){1}?$/|unique:franchises,gst_number,'.@($franchise->id == null ? null : $franchise->id)
        ];
        return $rules;
    }
}
