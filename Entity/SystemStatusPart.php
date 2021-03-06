<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Entity;

use Wakeapp\Component\OrmIdGenerator\Entity\IdAwareEntityTrait;
use Wakeapp\Component\OrmIdGenerator\Entity\IdAwareEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="SystemStatusPart"
 * )
 */
class SystemStatusPart implements IdAwareEntityInterface
{
    use IdAwareEntityTrait;
    use TimestampableEntity;


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
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $isLatest;

    /**
     * @return bool
     */
    public function isLatest(): bool
    {
        return $this->isLatest;
    }

    /**
     * @param bool $isLatest
     */
    public function setIsLatest(bool $isLatest): void
    {
        $this->isLatest = $isLatest;
    }

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
