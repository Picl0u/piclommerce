<?php

namespace Piclou\Piclommerce\Http\Entities;

use Illuminate\Database\Eloquent\Model;

class NewsletterContents extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'description',
        'image'
    ];
}
