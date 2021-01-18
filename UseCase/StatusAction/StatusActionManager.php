<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\UseCase\StatusAction;

use Wakeapp\Bundle\DbalBundle\Manager\DbalManager;

final class StatusActionManager extends DbalManager
{
    public function getStatusPartListByComponent(string $component): array
    {
        $sql = '
            SELECT
                ssp.completeScore as completeScore,
                ssp.currentScore as currentScore,
                ssp.partType as partType
            FROM SystemStatusPart ssp
            WHERE 1
                AND ssp.component = :component
        ';

        $stmt = $this->getConnection()->executeQuery($sql, [
            'component' => $component,
        ]);

        $rowList = [];
        while ($row = $stmt->fetch()) {
            $rowList[$row['partType']] = $row;
        }

        $stmt->closeCursor();

        return $rowList;
    }

    public function getStatusListByComponent(string $component): array
    {
        $sql = '
            SELECT
                ss.fineScore as fineScore,
                ss.currentScore as currentScore,
                ss.currentState as currentState,
                ss.component as component
            FROM SystemStatus ss
            WHERE 1
                AND ss.component = :component
        ';

        $stmt = $this->getConnection()->executeQuery($sql, [
            'component' => $component,
        ]);

        $row = $stmt->fetch();

        $stmt->closeCursor();

        return $row;
    }
}
