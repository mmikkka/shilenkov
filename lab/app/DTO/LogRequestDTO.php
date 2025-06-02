<?php

namespace App\DTO;

class LogRequestDTO
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $url,
        public readonly string  $method,
        public readonly ?string $controller,
        public readonly ?string $action,
        public readonly ?array  $requestBody,
        public readonly ?array  $requestHeaders,
        public readonly ?int    $userId,
        public readonly string  $ipAddress,
        public readonly ?string $userAgent,
        public readonly int     $statusCode,
        public readonly ?array  $responseBody,
        public readonly ?array  $responseHeaders,
        public readonly string  $calledAt
    )
    {
    }
}
