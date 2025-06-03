<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMessenger extends Model
{
    use HasFactory;

    protected $table = 'user_messengers';

    protected $fillable = [
        'user_id',
        'messenger_id',
        'messenger_user_id',
        'is_confirmed',
        'confirmed_at',
        'allow_notifications',
        'verification_code',
        'verification_code_sent_at',
        'verification_code_expires_at',
    ];

    protected $casts = [
        'is_confirmed' => 'boolean',
        'allow_notifications' => 'boolean',
        'confirmed_at' => 'datetime'
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
