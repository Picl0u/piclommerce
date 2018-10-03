<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Piclou\Piclommerce\Helpers\Translatable\HasTranslations;
use Ramsey\Uuid\Uuid;

class ContentCategory extends Model
{
    use HasTranslations;
    protected $fillable = ['id','uuid','on_footer','name'];

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
                'label' => __('piclommerce::admin.content_categories_name'),
                'type' => 'text'
            ],
        ];

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Contents()
    {
        return $this->hasMany(Content::class);
    }

}
