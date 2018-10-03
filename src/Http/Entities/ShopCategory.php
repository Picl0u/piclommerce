<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Piclou\Piclommerce\Helpers\Medias\HasMedias;
use Piclou\Piclommerce\Helpers\Translatable\HasTranslations;
use Ramsey\Uuid\Uuid;

class ShopCategory extends Model
{
    protected $guarded = [];
    use HasTranslations;
    use HasMedias;

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }

    public function translatable()
    {
        return  [
            'name' => [
                'label' => __('piclommerce::admin.shop_categories_name'),
                'type' => 'text'
            ],
            'slug' => [
                'label' => __('piclommerce::admin.seo_slug'),
                'type' => 'text'
            ],
            'description' => [
                'label' => __('piclommerce::admin.shop_categories_description'),
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ProductsHasCategories()
    {
        return $this->hasMany(ProductsHasCategory::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @param int $parentId
     * @return mixed
     */
    public function parentCategory($parentId)
    {
        if(!empty($parentId)){

            $parent = ShopCategory::where('id', $parentId)->First();
            if(!empty($parent->parent_id)){
                return self::parentCategory($parent->parent_id);
            }
            return $parent;
        }
        return null;
    }
}
