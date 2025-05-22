<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Pivot
{
    use SoftDeletes;

    protected $table = 'users_and_roles';

    protected $fillable = [
        'user_id',
        'role_id',
    ];

    protected array $dates = ['deleted_at'];
}
