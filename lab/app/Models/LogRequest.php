<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogRequest extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'url',
        'method',
        'controller',
        'action',
        'request_body',
        'request_headers',
        'user_id',
        'ip_address',
        'user_agent',
        'status_code',
        'response_body',
        'response_headers',
        'called_at'
    ];
    protected $casts = [
        'request_body' => 'array',
        'request_headers' => 'array',
        'response_body' => 'array',
        'response_headers' => 'array',
        'called_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
