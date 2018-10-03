<?php
namespace Piclou\Piclommerce\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SelectCarrier extends FormRequest
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
            'carrier_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'carrier_id.required' => __('piclommerce::validation.carrier_id_required'),
        ];
    }
}
