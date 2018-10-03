<?php

namespace Piclou\Piclommerce\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersAddresses extends FormRequest
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
            'firstname' => 'required',
            'lastname' => 'required',
            'address' => 'required',
            'zip_code' => 'required',
            'city' => 'required',
            'country_id' => 'required',
            'phone' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'firstname.required' => __('piclommerce::validation.firstname_required'),
            'lastname.required' => __('piclommerce::validation.lastname_required'),
            'address.required' => __('piclommerce::validation.address_required'),
            'zip_code.required' => __('piclommerce::validation.zip_code_required'),
            'city.required' => __('piclommerce::validation.city_required'),
            'country_id.required' => __('piclommerce::validation.country_id_required'),
            'phone.required' => __('piclommerce::validation.phone_required'),
        ];
    }
}