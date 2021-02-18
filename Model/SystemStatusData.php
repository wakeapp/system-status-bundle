<?php

namespace Wakeapp\Bundle\SystemStatusBundle\Model;

class SystemStatusData
{
    private string $component;

    private float $currentScore;

    private float $fineScore;

    private string $currentState;

    /**
     * @var SystemStatusPartData[]
     */
    private array $partList;

    /**
     * SystemStatusData constructor.
     * @param string $component
     */
    public function __construct(string $component)
    {
        $this->component = $component;
    }

    /**
     * @return string
     */
    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    /**
     * @param string $currentState
     */
    public function setCurrentState(string $currentState): void
    {
        $this->currentState = $currentState;
    }

    /**
     * @param float $currentScore
     */
    public function setCurrentScore(float $currentScore): void
    {
        $this->currentScore = $currentScore;
    }

    /**
     * @param float $fineScore
     */
    public function setFineScore(float $fineScore): void
    {
        $this->fineScore = $fineScore;
    }

    /**
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * @return SystemStatusPartData[]
     */
    public function getPartList(): array
    {
        return $this->partList;
    }

    /**
     * @return float
     */
    public function getCurrentScore(): float
    {
        return $this->currentScore;
    }

    /**
     * @return float
     */
    public function getFineScore(): float
    {
        return $this->fineScore;
    }

    public function addPart(SystemStatusPartData $partData): void
    {
        $this->partList[] = $partData;
    }
}