<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Piclou\Piclommerce\Helpers\Medias\HasMedias;
use Piclou\Piclommerce\Helpers\Translatable\HasTranslations;
use Ramsey\Uuid\Uuid;

class OrdersProducts extends Model
{

    use HasMedias;
    use HasTranslations;

    protected $fillable = [
        'uuid',
        'order_id',
        'product_id',
        'ref',
        'name',
        'image',
        'quantity',
        'price_ht',
        'price_ttc',
    ];

    /**
     * @return array
     */
    public function translatable()
    {
        return [
            'name' => [
                'label' => __('piclommerce::admin.shop_product_name'),
                'type' => 'text'
            ],
            'slug' => [
                'label' => __('piclommerce::admin.seo_slug'),
                'type' => 'text'
            ],

        ];
    }

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
    public function Order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function OrderReturns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Product()
    {
        return $this->belongsTo(Product::class);
    }

}
