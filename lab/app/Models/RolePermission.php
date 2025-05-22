<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'roles_and_permissions';
    protected array $fillable = [
        'role_id',
        'permission_id'
    ];
}
