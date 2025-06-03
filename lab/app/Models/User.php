<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use \Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Loggable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'birthday',
        'google2fa_enabled',
        'google2fa_secret',
    ];

    protected $hidden = [
        'google2fa_secret',
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

    public function hasPermission($permissionCode): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', fn($q) => $q->where('code', $permissionCode))
            ->exists();
    }

    public function hasRole($roleCode): bool
    {
        return $this->roles()->where('code', $roleCode)->exists();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'users_and_roles')
            ->using(UserRole::class)
            ->withPivot('deleted_at')
            ->wherePivotNull('deleted_at')
            ->whereNull('roles.deleted_at');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(LogRequest::class, 'user_id');
    }

    public function changes(): HasMany
    {
        return $this->hasMany(ChangeLog::class, 'created_by');
    }

    public function userMessengers(): HasMany
    {
        return $this->hasMany(UserMessenger::class);
    }
}
