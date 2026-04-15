<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'mobile_image',
        'link',
        'sort_order',
        'status',
        'placement',
    ];
}
