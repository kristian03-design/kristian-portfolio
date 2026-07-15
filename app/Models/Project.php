<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'image_path',
        'url',
        'github_url',
        'tech_stack',
        'metrics',
        'order',
        'category',
        'status',
        'duration',
        'role',
        'documentation_url',
        'video_demo_url',
        'documentation_status',
        'overview',
        'gallery',
        'features',
        'architecture',
        'challenges',
        'timeline',
        'performance',
        'security_details'
    ];

    protected $casts = [
        'tech_stack' => 'array',
        'metrics' => 'array',
        'overview' => 'array',
        'gallery' => 'array',
        'features' => 'array',
        'architecture' => 'array',
        'challenges' => 'array',
        'timeline' => 'array',
        'performance' => 'array',
        'security_details' => 'array',
    ];

    /**
     * Get two-letter initials computed from the project title.
     *
     * @return string
     */
    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->title));
        return collect($words)->filter()->take(2)->map(fn($word) => strtoupper(substr($word, 0, 1)))->implode('');
    }

    /**
     * Scope a query to only include visible projects.
     */
    public function scopeVisible($query)
    {
        return $query->where(function ($q) {
            $q->where('status', '!=', 'Draft')
              ->orWhereNull('status');
        });
    }
}
