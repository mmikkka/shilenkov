<?php

namespace App\DTO;

class LogRequestCollectionDTO
{
    public function __construct(
        public array $items,
        public int   $total,
        public int   $perPage,
        public int   $currentPage
    )
    {
    }
}
