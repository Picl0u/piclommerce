<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Piclou\Piclommerce\Helpers\Medias\HasMedias;
use Piclou\Piclommerce\Helpers\Translatable\HasTranslations;
use Ramsey\Uuid\Uuid;

class Slider extends Model
{
    use HasTranslations;
    use HasMedias;

    protected $fillable = ['id','uuid','published','name','description','image','link', 'position'];

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
                'label' => __('piclommerce::admin.slider_name'),
                'type' => 'text'
            ],
            'description' => [
                'label' =>  __('piclommerce::admin.slider_description'),
                'type' => 'editor'
            ],
        ];

    }

}
