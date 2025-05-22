<?php

namespace App\DTO;

class RoleDTO
{
    /**
     * @param string $name
     * @param string|null $description
     * @param string $code
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public string $code,
    ){}
}
