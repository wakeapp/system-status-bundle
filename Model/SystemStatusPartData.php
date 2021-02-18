<?php

namespace Wakeapp\Bundle\SystemStatusBundle\Model;

class SystemStatusPartData
{
    private string $component;

    private string $partType;

    private float $completeScore;

    private float $currentScore;

    /**
     * SystemStatusPartData constructor.
     * @param string $component
     * @param string $partType
     * @param float $completeScore
     * @param float $currentScore
     */
    public function __construct(
        string $component,
        string $partType,
        float $completeScore,
        float $currentScore
    ) {
        $this->component = $component;
        $this->partType = $partType;
        $this->completeScore = $completeScore;
        $this->currentScore = $currentScore;
    }
}
