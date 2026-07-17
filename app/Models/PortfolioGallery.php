<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioGallery extends Model
{
    protected $table = 'portfolio_gallery';

    protected $fillable = [
        'title',
        'short_description',
        'category',
        'image',
        'display_order',
        'is_featured',
        'is_published',
    ];

    protected $casts = [
        'image' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'display_order' => 'integer',
    ];
}
