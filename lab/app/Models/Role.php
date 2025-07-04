<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'code',
    ];

    protected array $dates = ['deleted_at'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_and_roles')
            ->using(UserRole::class)
            ->withPivot('deleted_at')
            ->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'roles_and_permissions')
            ->withTimestamps();
    }

    public function viewStory(User $user)
    {
        return $user->hasPermission('get-story-user') ||
            $user->hasPermission('get-story-role') ||
            $user->hasPermission('get-story-permission');
    }
}
