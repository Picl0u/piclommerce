<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Piclou\Piclommerce\Helpers\Attributes\HasAttributes;
use Ramsey\Uuid\Uuid;

class ProductsAttribute extends Model
{
    use HasAttributes;

    protected $fillable = [
        'uuid',
        'product_id',
        'stock_brut',
        'declinaisons',
        'reference',
        'ean_code',
        'upc_code',
        'isbn_code',
        'price_impact',
        'images'
    ];

    public $attr = [
        'declinaisons'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Product()
    {
        return $this->belongsTo(Product::class);
    }

}
