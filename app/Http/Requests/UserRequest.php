<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $user = User::find($this->employee);
        return [
            'name' => 'required',
            'email' =>'required|email|unique:users,email,'.@($user->id == null ? null : $user->id),
            'role_name' => 'required',
            'mobile' => 'required|numeric|regex:/^[0-9]{10}$/|unique:users,mobile,'.@($user->id == null ? null : $user->id),
            'address_line_one' => 'required',
            'city'  => 'required',
            'region'   => 'required',
            'country_id'   => 'required',
            'franchise_id'  => 'required'
        ];
    }
}
