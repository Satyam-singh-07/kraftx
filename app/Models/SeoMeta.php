<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'metaable_id',
        'metaable_type',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'canonical_url',
    ];

    public function metaable(): MorphTo
    {
        return $this->morphTo();
    }
}
