<?php

namespace Piclou\Piclommerce\Http\Entities;

use CyrildeWit\EloquentViewable\Viewable;
use Illuminate\Database\Eloquent\Model;
use Piclou\Piclommerce\Helpers\Medias\HasMedias;
use Piclou\Piclommerce\Helpers\Translatable\HasTranslations;
use Ramsey\Uuid\Uuid;

class Product extends Model
{
    use HasMedias;
    use HasTranslations;
    use Viewable;

    /**
     * @var array
     */
    protected $guarded = [];

    /*protected $dates = [
        'created_at',
        'updated_at',
        'reduce_date_begin',
        'reduce_date_end'
    ];*/

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }

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
            'summary' => [
                'label' => __('piclommerce::admin.shop_product_summary'),
                'type' => 'editor'
            ],
            'description' => [
                'label' => __('piclommerce::admin.shop_product_description'),
                'type' => 'editor'
            ],
            'seo_title' => [
                'label' => __('piclommerce::admin.seo_title'),
                'type' => 'text'
            ],
            'seo_description' => [
                'label' => __('piclommerce::admin.seo_description'),
                'type' => 'textarea'
            ],
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Vat()
    {
        return $this->belongsTo(Vat::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ShopCategory()
    {
        return $this->belongsTo(ShopCategory::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ProductsHasCategories()
    {
        return $this->hasMany(ProductsHasCategory::class)
            ->orderBy('shop_category_id','asc');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function OrdersProducts()
    {
        return $this->hasMany(OrdersProducts::class);
    }


    public static function FlashSales()
    {
        return Product::select(
            'products.id',
            'products.stock_available',
            'products.reduce_date_end',
            'products.reduce_price',
            'products.reduce_percent',
            'products.price_ttc',
            'products.image',
            'products.name',
            'products.slug',
            'products.summary',
            'products.updated_at',
            'shop_categories.name as category_name',
            'shop_categories.slug as category_slug',
            'shop_categories.id as category_id'
        )
        ->where('products.published',1)
        ->where('products.reduce_date_begin', '<=', date('Y-m-d H:i:s'))
        ->where('products.reduce_date_end', '>', date('Y-m-d H:i:s'))
        ->orderBy('products.reduce_date_end','ASC')
        ->join('shop_categories', 'shop_categories.id', '=', 'products.shop_category_id')
        ->limit(5)
        ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Comments()
    {
        return $this->hasMany(Comment::class)->where('published', 1);
    }

    /**
     * Retourne la liste des produits associés
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ProductsAssociates()
    {
        return $this->hasMany(ProductsAssociate::class, 'product_parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ProductsAttributes()
    {
        return $this->hasMany(ProductsAttribute::class);
    }


}
