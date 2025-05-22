<?php

namespace App\DTO;

class RolePermissionDTO
{
    public function __construct(
        public int $role_id,
        public int $permission_id
    ) {}
}
