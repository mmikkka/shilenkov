<?php

namespace App\DTO;

class ChangeLogDTO
{
    public function __construct(
        public string $entityType,
        public int    $entityId,
        public int    $created_by,
        public string $action,
        public ?array $before,
        public ?array $after,
        public ?bool  $is_rollbacked = false,
    )
    {
    }
}
