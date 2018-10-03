<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;

class OrdersStatus extends Model
{
    protected $fillable = [
        'status_id',
        'order_id'
    ];

    protected $table = 'orders_status';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Status()
    {
        return $this->belongsTo(Status::class);
    }
}
