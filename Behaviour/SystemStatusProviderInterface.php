<?php

namespace SystemStatusBundle\Behaviour;

interface SystemStatusProviderInterface
{
    /**
     * @return string
     */
    public function getComponentName(): string;

    /**
     * @return array
     */
    public static function getScoreMapping(): array;

    /**
     * @return string
     */
    public function getDefaultState(): string;

    /**
     * @return float
     */
    public static function getFineScore(): float;
}
