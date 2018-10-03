<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class CarriersPrices extends Model
{
    protected $fillable = [
        'uuid',
        'carriers_id',
        'country_id',
        'price_min',
        'price_max',
        'price',
        'key'
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
    public function Carrier()
    {
        return $this->belongsTo(Carriers::class, 'carriers_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Country()
    {
        return $this->belongsTo(Countries::class);
    }

}
