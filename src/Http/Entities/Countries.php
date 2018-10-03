<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function CarriersPrices()
    {
        return $this->belongsTo(CarriersPrices::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function UsersAdresses()
    {
        return $this->HasMany(UsersAdresses::class);
    }

}
