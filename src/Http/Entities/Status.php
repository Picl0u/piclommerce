<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Piclou\Piclommerce\Helpers\Translatable\HasTranslations;
use Ramsey\Uuid\Uuid;

class Status extends Model
{
    use HasTranslations;

    protected $fillable = [
        'uuid',
        'name',
        'color',
        'order_accept',
        'order_refuse'
    ];

    protected $table = 'status';

    public function translatable()
    {
        return  [
            'name' => [
                'label' => __('piclommerce::admin.order_status_name'),
                'type' => 'text'
            ],
        ];

    }

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
    public function Orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function OrdersStatus()
    {
        return $this->hasMany(OrdersStatus::class);
    }

}
