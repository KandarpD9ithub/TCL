<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
        $request=[
            'customer_id'   => 'sometimes|required',
            'order.*.product_id' => 'sometimes|required|exists:products,id',
            'order.*.quantity' => 'sometimes|required|numeric',
            'order.*.reason'    => 'sometimes|required',
            //'discount'  => 'sometimes|required|exists:discount_offer_rules,id'
        ];

        return $request;
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'Please Select Customer',
            'customer_id.exists' => 'Please Select Customer',
        ];
    }
}
