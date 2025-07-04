<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes, Loggable;

    protected $fillable = [
        'name',
        'description',
        'code',
    ];

    protected array $dates = ['deleted_at'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'roles_and_permissions')
            ->withPivot(['created_at', 'deleted_at'])
            ->withTimestamps();
    }

    public function viewStory(User $user)
    {
        return $user->hasPermission('get-story-user') ||
            $user->hasPermission('get-story-role') ||
            $user->hasPermission('get-story-permission');
    }
}
