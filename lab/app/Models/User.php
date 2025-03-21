<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

class User extends Model
{
    use HasApiTokens;
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'birthday',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'date', // если нужно, чтобы birthday воспринималась как дата
    ];

    public static function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function removeExpiredTokens(): void
    {
        PersonalAccessToken::query()->where('tokenable_id', $this->id)
            ->where('expires_at', '<', now())
            ->where('id', '!=', $this->currentAccessToken()->id ?? null)
            ->delete();
    }

}
