<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Coupon extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'coupon',
        'percent',
        'price',
        'use_max',
        'amount_min',
        'begin',
        'end'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }

    public function CouponUsers()
    {
        return $this->hasMany(CouponUser::class);
    }

    public function CouponProducts()
    {
        return $this->hasMany(CouponProduct::class);
    }

}
