<?php

namespace App\DTO;

class UserRoleDTO
{
    public function __construct(
        public int $user_id,
        public int $role_id
    ) {}
}
