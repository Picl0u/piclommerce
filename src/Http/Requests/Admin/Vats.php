<?php

namespace Piclou\Piclommerce\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class Vats extends FormRequest
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
        return [
            'name' => 'required',
            'percent' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __("piclommerce::admin.shop_vat_error_name"),
            'percent.required' => __("piclommerce::admin.shop_vat_error_percent"),
            'percent.numeric' => __("piclommerce::admin.shop_vat_error_percent_numeric"),
        ];
    }
}
