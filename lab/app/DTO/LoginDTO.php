<?php

namespace App\DTO;

use Illuminate\Support\Facades\Hash;

class LoginDTO
{
    public function __construct(
        public string $username,
        public string $password
    ) {}
}
