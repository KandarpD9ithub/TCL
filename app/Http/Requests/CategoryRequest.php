<?php
/**
 * @package App\Http\Requests
 *
 * @class CategoryRequest
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App\Http\Requests;

use App\Category;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $category = Category::find($this->category);
        return [
            'name' => 'required|unique:categories,name,'.@($category->id == null ? null : $category->id),
            'tax_id' => 'required'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'tax_id.required' => 'Tax profile field is required'
        ];
    }
}
