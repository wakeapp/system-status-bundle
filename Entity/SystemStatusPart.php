<?php

declare(strict_types=1);

namespace SystemStatusBundle\Entity;

use Domain\Model\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="SystemStatusPart",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="componentUniq",columns={"component", "partType"})
 *     }
 * )
 */
class SystemStatusPart extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $component;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $partType;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private float $completeScore;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private float $currentScore;

    /**
     * @return float
     */
    public function getCompleteScore(): float
    {
        return $this->completeScore;
    }

    /**
     * @param $completeScore
     * @return void
     */
    public function setCompleteScore($completeScore): void
    {
        $this->completeScore = $completeScore;
    }

    /**
     * @return float
     */
    public function getCurrentScore(): float
    {
        return $this->currentScore;
    }

    /**
     * @param $currentScore
     * @return void
     */
    public function setCurrentScore($currentScore): void
    {
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
     * @param $component
     * @return void
     */
    public function setComponent($component): void
    {
        $this->component = $component;
    }

    /**
     * @return string
     */
    public function getPartType(): string
    {
        return $this->partType;
    }

    /**
     * @param $partType
     * @return void
     */
    public function setPartType($partType): void
    {
        $this->partType = $partType;
    }
}
