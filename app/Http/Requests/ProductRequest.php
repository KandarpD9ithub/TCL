<?php
/**
 * @package App\Http\Requests
 *
 * @class ProductRequest
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App\Http\Requests;

use App\Product;
use App\ProductPhotos;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $products = Product::find($this->product);
        $rules =  [
            'name'  => 'required|unique:products,name,'.@($products->id == null ? null : $products->id),
            'price' => 'required',
            'product_code' => 'required|unique:products,product_code,'.@($products->id == null ? null : $products->id),
            'image' => 'image|mimes:jpeg,jpg,png|max:2048',
            'tag_id' => 'required|array'

        ];
        return $rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'product_code.required' => 'The SKU field is required',
            'product_code.unique'   => 'The SKU has already been taken.'
        ];
    }
}
