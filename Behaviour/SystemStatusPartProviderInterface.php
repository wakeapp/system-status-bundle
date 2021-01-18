<?php

namespace SystemStatusBundle\Behaviour;

interface SystemStatusPartProviderInterface
{
    /**
     * @return float
     */
    public function check(): float;

    /**
     * @return string
     */
    public function getPartTypeName(): string;

    /**
     * @return string
     */
    public function getComponentName(): string;

    /**
     * @return float
     */
    public function getCompleteScore(): float;
}
