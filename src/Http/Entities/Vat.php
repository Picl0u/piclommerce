<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Vat extends Model
{
    protected $fillable = ['id','uuid','name','percent','updated_date','created_at'];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }

    public function Products()
    {
        return $this->hasMany(Product::class);
    }

}
