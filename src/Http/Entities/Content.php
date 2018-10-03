<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Piclou\Piclommerce\Helpers\Medias\HasMedias;
use Piclou\Piclommerce\Helpers\Translatable\HasTranslations;
use Ramsey\Uuid\Uuid;

class Content extends Model
{
    use HasTranslations;
    use HasMedias;

    protected $fillable = [
        'id',
        'uuid',
        'published',
        'on_homepage',
        'on_footer',
        'on_menu',
        'name',
        'slug',
        'image',
        'content_category_id',
        'summary',
        'description',
        'order',
        'seo_title',
        'seo_description',
        'seo_keywords'
    ];

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
                'label' => __('piclommerce::admin.content_pages_name'),
                'type' => 'text'
            ],
            'slug' => [
                'label' => __('piclommerce::admin.seo_slug'),
                'type' => 'text'
            ],
            'summary' => [
                'label' => __('piclommerce::admin.content_pages_summary'),
                'type' => 'editor'
            ],
            'description' => [
                'label' => __('piclommerce::admin.content_pages_description'),
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
    public function ContentCategory()
    {
        return $this->belongsTo(ContentCategory::class);
    }

}
