<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductsAssociate extends Model
{
    protected $fillable = [
        'product_id',
        'product_parent'
    ];

    /**
     * Retourne le produit associé
     * Return the associate product
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Product()
    {
        return $this->belongsTo(Product::class)->where('published',1);
    }

    /**
     * Retourne le produit référent
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Parent()
    {
        return $this->belongsTo(Product::class, 'product_parent');
    }

}
