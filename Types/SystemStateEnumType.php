<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Types;

use Wakeapp\Bundle\SystemStatusBundle\Enum\SystemStateEnum;
use Wakeapp\Component\DbalEnumType\Type\AbstractEnumType;

class SystemStateEnumType extends AbstractEnumType
{
    public const NAME = 'system_state';

    /**
     * {@inheritDoc}
     */
    public static function getEnumClass(): string
    {
        return SystemStateEnum::class;
    }

    /**
     * {@inheritDoc}
     */
    public static function getTypeName(): string
    {
        return self::NAME;
    }
}
