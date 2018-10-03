<?php
namespace Piclou\Piclommerce\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpressUsers extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'express_firstname' => 'required',
            'express_lastname' => 'required',
            'express_email' => 'required|email'
        ];
    }

    public function messages()
    {
        return [
            'express_firstname.required' => __('piclommerce::validation.firstname_required'),
            'express_lastname.required' => __('piclommerce::validation.lastname_required'),
            'express_email.required' => __('piclommerce::validation.email_required'),
            'express_email.email' => __('piclommerce::validation.email_format'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
