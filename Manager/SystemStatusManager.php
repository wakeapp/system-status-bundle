<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Manager;

use Wakeapp\Bundle\DbalBundle\Exception\WriteDbalException;
use Wakeapp\Bundle\DbalBundle\Manager\DbalManager;

final class SystemStatusManager extends DbalManager
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
        $queryBuilder->andWhere('isLatest = 1');
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

        $this->updateLatestSystemStatus($component);

        $params = [
            'currentState' => $state,
            'currentScore' => $score,
            'component' => $component,
            'fineScore' => $fineScore,
            'isLatest' => 1,
        ];

        $this->insert(
            'SystemStatus',
            $params,
        );
    }

    /**
     * @param array $paramsList
     *
     * @throws WriteDbalException
     */
    public function insertSystemStatusPartList(array $paramsList): void
    {
        $componentPartTypeList = [];

        foreach ($paramsList as $param) {
            $componentPartTypeList[] = [
                'isLatest' => 0,
                'component' => $param['component'],
                'partType' => $param['partType'],
            ];
        }

        $this->updateLatestSystemStatusPartList(array_unique($componentPartTypeList, SORT_REGULAR));

        $this->insertBulk(
            'SystemStatusPart',
            $paramsList,
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
        $this->insertSystemStatusPartList([
            [
                'currentScore' => $currentScore,
                'completeScore' => $completeScore,
                'component' => $component,
                'partType' => $partType,
                'isLatest' => 1,
            ]
        ]);
    }

    /**
     * @param string $component
     */
    public function updateLatestSystemStatus(string $component): void
    {
        $this->update(
            'SystemStatus',
            [
                'isLatest' => 0,
            ],
            [
                'component' => $component,
            ]
        );
    }

    /**
     * @param array $whereFields
     * @throws WriteDbalException
     */
    public function updateLatestSystemStatusPartList(array $componentPartTypeList): void
    {
        $this->updateBulk(
            'SystemStatusPart',
            $componentPartTypeList,
            [
                'component',
                'partType',
            ]
        );
    }
}
