<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\DependencyInjection;

use Exception;
use Symfony\Bundle\MonologBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Wakeapp\Bundle\SystemStatusBundle\Behaviour\SystemStatusPartProviderFactoryInterface;
use Wakeapp\Bundle\SystemStatusBundle\Behaviour\SystemStatusPartProviderInterface;
use Wakeapp\Bundle\SystemStatusBundle\Behaviour\SystemStatusProviderInterface;
use Wakeapp\Bundle\SystemStatusBundle\DependencyInjection\SystemStatusConfiguration;

class SystemStatusExtension extends Extension implements PrependExtensionInterface
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

        $configuration = new SystemStatusConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('system_status.api_key', $config['api_key']);

        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(SystemStatusProviderInterface::class)
            ->addTag('system_status_provider');
        $container->registerForAutoconfiguration(SystemStatusPartProviderInterface::class)
            ->addTag('system_status_part_provider');
        $container->registerForAutoconfiguration(SystemStatusPartProviderFactoryInterface::class)
            ->addTag('system_status_part_provider_factory');
    }

    /**
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['MonologBundle'])) {
            $configs = [
                [
                    'handlers' => [
                        'system_status' => [
                            'type' => 'stream',
                            'path' => '%kernel.logs_dir%/%kernel.environment%_system_status.log',
                            'bubble' => false,
                            'level' => 'debug',
                            'channels' => ['system_status'],
                        ],
                    ],
                ],
            ];

            $config = $this->processConfiguration(new Configuration(), $configs);

            $container->prependExtensionConfig('monolog', $config);
        }
    }
}
