<?php

namespace App\DTO;

class PermissionDTO
{
    public function __construct(
        public string $name,
        public string $code,
        public ?string $description = null
    ) {}
}
