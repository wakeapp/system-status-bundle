<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Service;

use Wakeapp\Bundle\SystemStatusBundle\Behaviour\SystemStatusPartProviderFactoryInterface;
use Wakeapp\Bundle\SystemStatusBundle\Behaviour\SystemStatusPartProviderInterface;
use RuntimeException;
use Wakeapp\Bundle\SystemStatusBundle\Behaviour\SystemStatusProviderInterface;

final class SystemStatusPartService
{
    /**
     * @var SystemStatusPartProviderInterface[]
     */
    private array $systemStatusPartProviderPool = [];

    /**
     * @var SystemStatusPartProviderFactoryInterface[]
     */
    private array $systemStatusPartProviderFactoryPool = [];

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
     * @param SystemStatusPartProviderFactoryInterface $systemStatusPartProviderFactory
     */
    public function addFactory(SystemStatusPartProviderFactoryInterface $systemStatusPartProviderFactory): void
    {
        $componentName = $systemStatusPartProviderFactory->getComponentName();

        $this->systemStatusPartProviderFactoryPool[$componentName] = $systemStatusPartProviderFactory;
    }

    /**
     * @param string $componentName
     */
    public function getProvidersFromPool(string $componentName)
    {
        return $this->systemStatusPartProviderPool[$componentName] ?? [];
    }

    /**
     * @param string $componentName
     *
     * @return SystemStatusPartProviderInterface[]
     */
    public function getParts(string $componentName): array
    {
        /** @var SystemStatusPartProviderFactoryInterface $factory */
        $factory = $this->systemStatusPartProviderFactoryPool[$componentName] ?? null;

        if ($factory) {
            $parts = $factory->initParts();
        } else {
            $parts = $this->getProvidersFromPool($componentName);
        }

        if (!$parts) {
            throw new RuntimeException(
                strtr(
                    'System Status Provider Parts or Factory not found for component ":componentName"',
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
