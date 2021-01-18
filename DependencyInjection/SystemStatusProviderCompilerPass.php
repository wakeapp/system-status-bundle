<?php

declare(strict_types=1);

namespace SystemStatusBundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SystemStatusBundle\Service\SystemStatusService;

class SystemStatusProviderCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function process(ContainerBuilder $container): void
    {
        $taggedServiceList = $container->findTaggedServiceIds('system_status_provider');

        $service = $container->findDefinition(SystemStatusService::class);
        foreach ($taggedServiceList as $serviceId => $tagList) {
            $poolDefinition = $container->getDefinition($serviceId);
            $service->addMethodCall('addProvider', [$poolDefinition]);
        }
    }
}
