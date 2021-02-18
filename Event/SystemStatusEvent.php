<?php

namespace Wakeapp\Bundle\SystemStatusBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Wakeapp\Bundle\SystemStatusBundle\Model\SystemStatusData;

class SystemStatusEvent extends Event
{
    private SystemStatusData $systemStatusData;

    public function __construct(
        SystemStatusData $systemStatusData
    ) {
        $this->systemStatusData = $systemStatusData;
    }

    /**
     * @return SystemStatusData
     */
    public function getSystemStatusData(): SystemStatusData
    {
        return $this->systemStatusData;
    }
}
