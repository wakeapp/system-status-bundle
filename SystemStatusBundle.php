<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle;

use Wakeapp\Bundle\SystemStatusBundle\DependencyInjection\SystemStatusPartProviderCompilerPass;
use Wakeapp\Bundle\SystemStatusBundle\DependencyInjection\SystemStatusPartProviderFactoryCompilesPass;
use Wakeapp\Bundle\SystemStatusBundle\DependencyInjection\SystemStatusProviderCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SystemStatusBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new SystemStatusPartProviderCompilerPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            1
        );

        $container->addCompilerPass(
            new SystemStatusPartProviderFactoryCompilesPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            1
        );

        $container->addCompilerPass(
            new SystemStatusProviderCompilerPass(),
            PassConfig::TYPE_BEFORE_REMOVING,
            2
        );
    }
}
