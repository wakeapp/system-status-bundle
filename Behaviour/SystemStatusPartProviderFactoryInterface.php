<?php

namespace Wakeapp\Bundle\SystemStatusBundle\Behaviour;

interface SystemStatusPartProviderFactoryInterface
{
    /**
     * @return SystemStatusPartProviderInterface[]
     */
    public function initParts(): array;

    /**
     * @return string
     */
    public function getComponentName(): string;
}
