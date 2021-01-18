<?php

declare(strict_types=1);

namespace SystemStatusBundle\Service;

use SystemStatusBundle\Behaviour\SystemStatusPartProviderInterface;
use RuntimeException;

class SystemStatusPartService
{
    /**
     * @var array
     */
    private array $systemStatusPartProviderPool = [];

    /**
     * @param SystemStatusPartProviderInterface $systemStatusPartProvider
     */
    public function addProvider(SystemStatusPartProviderInterface $systemStatusPartProvider): void
    {
        $componentName = $systemStatusPartProvider->getComponentName();
        $partName = $systemStatusPartProvider->getPartTypeName();
        $this->systemStatusPartProviderPool[$componentName][$partName] = $systemStatusPartProvider;
    }

    /**
     * @param string $componentName
     *
     * @return SystemStatusPartProviderInterface[]
     */
    public function getParts(string $componentName): array
    {
        $parts = $this->systemStatusPartProviderPool[$componentName] ?? null;
        if (!$parts) {
            throw new RuntimeException(
                strtr(
                    'System Status Provider Poll not found for component :componentName',
                    [
                        ':componentName' => $componentName
                    ]
                )
            );
        }

        return $parts;
    }

    /**
     * @param string $componentName
     *
     * @return int
     */
    public function getPartProviderCount(string $componentName): int
    {
        $parts = $this->systemStatusPartProviderPool[$componentName] ?? [];

        return count($parts);
    }
}
