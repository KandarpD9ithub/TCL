<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProductPriceRequest extends FormRequest
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
        $rules = [];
        if (Request::isMethod('post')) {
            $rules = [
                'product_price.*.product_id' => 'distinct|required|unique:product_prices,product_id',
                'product_price.*.price' => 'required||numeric|digits_between:1,5'
            ];
        } else {
            $rules = [
                'price' => 'required|numeric|digits_between:1,5'
            ];
        }
        return $rules;
    }
}
