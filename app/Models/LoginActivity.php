<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginActivity extends Model
{
    protected $table = 'login_activities';

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'browser',
        'operating_system',
        'device',
        'success',
        'failure_reason',
        'type',
    ];

    protected $casts = [
        'success' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
