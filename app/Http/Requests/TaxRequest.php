<?php

namespace App\Http\Requests;

use App\Tax;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class TaxRequest extends FormRequest
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
        $input = Input::all();
        $tax = isset($this->tax->id) ? $this->tax->id : null;
        $rules =  [
            'tax_name'  => 'required|unique:taxes,tax_name,'.$tax,
            'tax_type'  => 'required',
        ];

        return $rules;
    }
}
