<?php

namespace Wakeapp\Bundle\SystemStatusBundle\Behaviour;

interface SystemStatusProviderInterface
{
    /**
     * @return string
     */
    public function getComponentName(): string;

    /**
     * @return array
     */
    public function getScoreMapping(): array;

    /**
     * @return string
     */
    public function getDefaultState(): string;

    /**
     * @return float
     */
    public function getFineScore(): float;

    /**
     * @return bool
     */
    public function needDispatchEvent(): bool;
}
