<?php
namespace Piclou\Piclommerce\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class Sliders extends FormRequest
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
            'image' => 'image'
        ];
    }

    public function messages()
    {
        return [
            'name.required' =>__("piclommerce::admin.slider_error_name"),
        ];
    }
}
