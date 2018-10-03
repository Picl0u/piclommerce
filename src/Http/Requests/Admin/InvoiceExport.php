<?php

namespace Piclou\Piclommerce\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceExport extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date_begin' => 'date',
            'date_end' => 'date',
        ];
    }

    public function messages()
    {
        return [
            'date_begin.date' => __("piclommerce::admin.order_export_date_begin"),
            'date_end.date' => __("piclommerce::admin.order_export_date_end")
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