<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Piclou\Piclommerce\Helpers\Medias\HasMedias;

class Carriers extends Model
{
    use HasMedias;

    protected $fillable = [
        'published',
        'uuid',
        'free',
        'price',
        'weight',
        'name',
        'delay',
        'image',
        'url',
        'max_weight',
        'max_width',
        'max_height',
        'max_length',
        'default',
        'default_price'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function CarriersPrices()
    {
        return $this->hasMany(CarriersPrices::class, 'carriers_id');
    }
}
