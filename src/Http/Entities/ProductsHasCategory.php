<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductsHasCategory extends Model
{
    protected $fillable = ['id', 'product_id', 'shop_category_id'];

    public $timestamps = false;

    public function Product()
    {
        return $this->belongsTo(Product::class);
    }
    public function ShopCategory()
    {
        return $this->belongsTo(ShopCategory::class);
    }
}
