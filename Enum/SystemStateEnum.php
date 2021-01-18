<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Enum;

use Wakeapp\Bundle\EnumerBundle\Enum\EnumInterface;

class SystemStateEnum implements EnumInterface
{
    public const GREAT = 'great';
    public const FINE = 'fine';
    public const WARNING = 'warning';
    public const CRITICAL = 'critical';
}
