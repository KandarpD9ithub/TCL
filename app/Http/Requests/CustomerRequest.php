<?php

namespace App\Http\Requests;

use App\Customer;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        $customer = Customer::find($this->customer);
        return [
            'name'  =>  'required|max:100|alpha',
            'contact_number' => 'required|numeric|regex:/^[0-9]{10}$/|unique:customers,contact_number,'.@($customer->id == null ? null : $customer->id),
            'email' => 'max:150|email|unique:customers,email,'.@($customer->id == null ? null : $customer->id),
            'profile_picture'   => 'image',
            'address_line_one'  => 'max:100',
            'address_line_two'  => 'max:100',
            'city'              => 'max:50',
            'region'            => 'max:50',
            'country_id'        => '',
        ];
    }
}
