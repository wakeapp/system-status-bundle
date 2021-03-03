<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wakeapp\Bundle\SystemStatusBundle\Service\SystemStatusPartService;

class SystemStatusPartProviderFactoryCompilesPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $taggedServiceList = $container->findTaggedServiceIds('system_status_part_provider_factory');

        $service = $container->findDefinition(SystemStatusPartService::class);
        foreach ($taggedServiceList as $serviceId => $tagList) {
            $poolDefinition = $container->getDefinition($serviceId);
            $service->addMethodCall('addFactory', [$poolDefinition]);
        }
    }
}
