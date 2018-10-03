<?php
namespace Piclou\Piclommerce\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderCarrierRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'shipping_url' => 'required|url',
            'shipping_order_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'shipping_url.required' => __("piclommerce::admin.orders_carrier_url_required"),
            'shipping_order_id' => __("piclommerce::web.orders_carrier_id_required"),
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