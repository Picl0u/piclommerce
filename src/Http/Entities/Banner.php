<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Piclou\Piclommerce\Helpers\Medias\HasMedias;
use Ramsey\Uuid\Uuid;

class Banner extends Model
{
    use HasMedias;

    protected $fillable = [
        'id',
        'uuid',
        'published',
        'name',
        'link',
        'image',
        'order',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }
}
