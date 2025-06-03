<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'environment',
        'token_env_name'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_messengers')
            ->withPivot(['messenger_user_id', 'is_confirmed', 'confirmed_at', 'allow_notifications'])
            ->withTimestamps();
    }
}
