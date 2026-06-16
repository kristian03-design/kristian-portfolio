<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['title', 'description', 'image_path', 'url', 'github_url', 'tech_stack', 'metrics', 'order'];

    protected $casts = [
        'tech_stack' => 'array',
        'metrics' => 'array',
    ];
}
