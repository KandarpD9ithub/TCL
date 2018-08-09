<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
            'menu.*.category_id' => 'distinct|required',
            'menu.*.product_id.*' => 'distinct|required|unique:menu,product_id'
        ];

        return $request;
    }

    public function messages()
    {
        $request =[
            'menu.*.category_id' => 'Category is Required',
            'menu.*.product_id.*.required' => 'Product is Required',
            'menu.*.product_id.*.unique' => 'Product must be Unique'
        ];
        return $request;
    }
}
