<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
    protected $fillable =[
        'uuid',
        'reference',
        'price_ht',
        'vat_price',
        'vat_percent',
        'shipping_name',
        'shipping_delay',
        'shipping_url',
        'shipping_price',
        'price_ttc',
        'total_quantity',
        'user_id',
        'user_firstname',
        'user_lastname',
        'user_email',
        'delivery_gender',
        'delivery_firstname',
        'delivery_lastname',
        'delivery_address',
        'delivery_additional_address',
        'delivery_zip_code',
        'delivery_city',
        'delivery_country_id',
        'delivery_country_name',
        'delivery_phone',
        'billing_gender',
        'billing_firstname',
        'billing_lastname',
        'billing_address',
        'billing_additional_address',
        'billing_zip_code',
        'billing_city',
        'billing_country_id',
        'billing_country_name',
        'billing_phone',
        'status_id',
        'shipping_order_id',
        'coupon_id',
        'coupon_price',
        'coupon_name'
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
    public function OrdersProducts()
    {
        return $this->hasMany(OrdersProducts::class,'order_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function OrdersStatus()
    {
        return $this->hasMany(OrdersStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function OrdersReturns()
    {
        return $this->hasMany(OrderReturn::class);
    }

}
