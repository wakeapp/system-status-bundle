<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Manager;

use Wakeapp\Bundle\DbalBundle\Exception\WriteDbalException;
use Wakeapp\Bundle\DbalBundle\Manager\DbalManager;

class SystemStatusManager extends DbalManager
{
    /**
     * @param string $component
     * @return array
     */
    public function getSystemStatusParts(string $component): array
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();
        $queryBuilder->select('ssp.partType', 'ssp.completeScore', 'ssp.currentScore');
        $queryBuilder->from('SystemStatusPart ssp');
        $queryBuilder->andWhere('component = :component');
        $queryBuilder->setParameter('component', $component);
        $stmt = $queryBuilder->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param string $component
     * @param string $state
     * @param float $score
     * @param float $fineScore
     *
     * @return void
     *
     * @throws WriteDbalException
     */
    public function upsertSystemStatus(
        string $component,
        string $state,
        float $score,
        float $fineScore
    ): void {
        $params = [
            'currentState' => $state,
            'currentScore' => $score,
            'component' => $component,
            'fineScore' => $fineScore,
        ];

        $this->upsert(
            'SystemStatus',
            $params,
            [
                'currentState',
                'currentScore',
                'fineScore',
            ]
        );
    }

    /**
     * @param array $paramsList
     *
     * @throws WriteDbalException
     */
    public function upsertSystemStatusPartList(array $paramsList): void
    {
        $this->upsertBulk(
            'SystemStatusPart',
            $paramsList,
            [
                'currentScore',
                'completeScore',
            ]
        );
    }

    /**
     * @param string $component
     * @param string $partType
     * @param float $currentScore
     * @param int|null $completeScore
     * @return void
     * @throws WriteDbalException
     */
    public function upsertSystemStatusPart(
        string $component,
        string $partType,
        float $currentScore,
        int $completeScore = null
    ): void {
        $this->upsertSystemStatusPartList([
            [
                'currentScore' => $currentScore,
                'completeScore' => $completeScore,
                'component' => $component,
                'partType' => $partType,
            ]
        ]);
    }
}
