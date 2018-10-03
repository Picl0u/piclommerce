<?php

namespace Piclou\Piclommerce\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SelectAddresses extends FormRequest
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
            'delivery_address' => 'required',
            'billing_address' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'delivery_address.required' => trans('piclommerce::validation.delivery_address_required'),
            'billing_address.required' => trans('piclommerce::validation.billing_address_required'),
        ];
    }
}