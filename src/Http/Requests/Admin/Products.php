<?php
namespace Piclou\Piclommerce\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class Products extends FormRequest
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
            'description' => 'required',
            'image' => 'image',
            'shop_category_id' => 'required|integer',
            'reference' => 'required',
            'price_ht' => 'required|numeric',
            'price_ttc' => 'required|numeric'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => __("piclommerce::admin.shop_product_name_required"),
            'description.required' => __("piclommerce::admin.shop_product_description_required"),
            'shop_category_id.required' => __("piclommerce::admin.shop_product_category_id_required"),
            'reference.required' => __("piclommerce::admin.shop_product_reference_required"),
            'price_ht.required' => __("piclommerce::admin.shop_product_price_ht_required"),
            'price_ht.numeric' => __("piclommerce::admin.shop_product_price_ht_numeric"),
            'price_ttc.required' => __("piclommerce::admin.shop_product_price_ttc_required"),
            'price_ttc.numeric' => __("piclommerce::admin.shop_product_price_ttc_numeric"),
        ];
    }
}