<?php

declare(strict_types=1);

namespace SystemStatusBundle\DependencyInjection;

use SystemStatusBundle\Behaviour\SystemStatusPartProviderInterface;
use SystemStatusBundle\Behaviour\SystemStatusProviderInterface;
use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SystemStatusExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(SystemStatusProviderInterface::class)
            ->addTag('system_status_provider');
        $container->registerForAutoconfiguration(SystemStatusPartProviderInterface::class)
            ->addTag('system_status_part_provider');
    }
}
