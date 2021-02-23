<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Wakeapp\Component\OrmIdGenerator\Entity\IdAwareEntityTrait;
use Wakeapp\Component\OrmIdGenerator\Entity\IdAwareEntityInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="SystemStatus",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="componentUniq",columns={"component"})
 *     }
 * )
 */
class SystemStatus implements IdAwareEntityInterface
{
    use TimestampableEntity;
    use IdAwareEntityTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $component;

    /**
     * @var string
     *
     * @ORM\Column(type="system_state")
     */
    private $currentState;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private float $fineScore;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private float $currentScore;

    /**
     * @return string
     */
    public function getComponent(): ?string
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
    public function getCurrentState(): ?string
    {
        return $this->currentState;
    }

    /**
     * @param $currentState
     * @return void
     */
    public function setCurrentState($currentState): void
    {
        $this->currentState = $currentState;
    }

    /**
     * @return float
     */
    public function getFineScore(): float
    {
        return $this->fineScore;
    }

    /**
     * @param $findScore
     * @return void
     */
    public function setFineScore($findScore): void
    {
        $this->fineScore = $findScore;
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
}
