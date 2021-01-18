<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\UseCase\StatusAction;

use JsonException;

use function json_encode;

final class StatusActionHandler
{
    protected StatusActionManager $manager;

    public function __construct(StatusActionManager $manager)
    {
        $this->manager = $manager;
    }

    public function handle(string $componentName): array
    {
        if (empty($componentName)) {
            return [];
        }

        $statusPartList = $this->manager->getStatusPartListByComponent($componentName);
        $statusList = $this->manager->getStatusListByComponent($componentName);

        $statusList['parts'] = $statusPartList;

        return $statusList;
    }
}
