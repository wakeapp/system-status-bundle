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

    /**
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * @return string
     */
    public function getPartType(): string
    {
        return $this->partType;
    }

    /**
     * @return float
     */
    public function getCompleteScore(): float
    {
        return $this->completeScore;
    }

    /**
     * @return float
     */
    public function getCurrentScore(): float
    {
        return $this->currentScore;
    }
}
