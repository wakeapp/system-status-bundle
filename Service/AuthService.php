<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Service;

final class AuthService
{
    protected string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function authorize(string $apiKey): bool
    {
        return $apiKey === $this->apiKey;
    }
}
