<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessengerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'messenger_id',
        'message',
        'status',
        'attempt_number'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messenger(): BelongsTo
    {
        return $this->belongsTo(Messenger::class);
    }
}
