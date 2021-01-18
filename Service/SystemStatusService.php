<?php

declare(strict_types=1);

namespace SystemStatusBundle\Service;

use SystemStatusBundle\Behaviour\SystemStatusProviderInterface;
use SystemStatusBundle\Enum\SystemStateEnum;
use SystemStatusBundle\Manager\SystemStatusManager;
use RuntimeException;
use Wakeapp\Bundle\DbalBundle\Exception\WriteDbalException;

class SystemStatusService
{
    /**
     * @var SystemStatusManager
     */
    private SystemStatusManager $manager;

    /**
     * @var SystemStatusPartService
     */
    private SystemStatusPartService $systemStatusPartService;

    /**
     * @var SystemStatusProviderInterface[]
     */
    private array $systemStatusProviderPool = [];

    /**
     * @param SystemStatusManager $manager
     * @param SystemStatusPartService $systemStatusPartService
     */
    public function __construct(
        SystemStatusManager $manager,
        SystemStatusPartService $systemStatusPartService
    ) {
        $this->manager = $manager;
        $this->systemStatusPartService = $systemStatusPartService;
    }

    /**
     * @param SystemStatusProviderInterface $systemStatusProvider
     */
    public function addProvider(SystemStatusProviderInterface $systemStatusProvider): void
    {
        $this->systemStatusProviderPool[$systemStatusProvider->getComponentName()] = $systemStatusProvider;
    }

    /**
     * @return SystemStatusProviderInterface[]
     */
    public function getProviderList(): array
    {
        return $this->systemStatusProviderPool;
    }

    /**
     * @param string $componentName
     * @throws WriteDbalException
     */
    public function processSystemStatus(string $componentName): void
    {
        $systemStatusProvider = $this->systemStatusProviderPool[$componentName];
        if (!$systemStatusProvider) {
            throw new RuntimeException(
                strtr(
                    'System Status Provider not found to component :componentName',
                    [
                        ':componentName' => $componentName
                    ]
                )
            );
        }

        $this->checkSystemStatus($systemStatusProvider);
    }

    /**
     * @param SystemStatusProviderInterface $systemStatusProvider
     *
     * @return void
     *
     * @throws WriteDbalException
     */
    private function checkSystemStatus(SystemStatusProviderInterface $systemStatusProvider): void
    {
        $finalCurrentScore = 0;
        $componentName = $systemStatusProvider->getComponentName();
        $scoreMapping = $systemStatusProvider->getScoreMapping();
        $currentState = $systemStatusProvider->getDefaultState();
        $partsProviderCount = $this->systemStatusPartService->getPartProviderCount($componentName);
        $haveCritical = false;
        $paramsList = [];

        $systemStatusPartList = $this->systemStatusPartService->getParts($componentName);
        foreach ($systemStatusPartList as $systemStatusPart) {
            $currentScore = $systemStatusPart->check() / $partsProviderCount;
            $paramsList[] = [
                'component' => $systemStatusPart->getComponentName(),
                'partType' => $systemStatusPart->getPartTypeName(),
                'currentScore' => $currentScore,
                'completeScore' => $systemStatusPart->getCompleteScore() / $partsProviderCount,
            ];

            $finalCurrentScore += $currentScore;

            if ((int)$currentScore <= 0) {
                $haveCritical = true;
            }
        }

        $this->manager->upsertSystemStatusPartList($paramsList);

        if ($haveCritical) {
            $currentState = SystemStateEnum::CRITICAL;
        } else {
            foreach ($scoreMapping as $state => $data) {
                if ($finalCurrentScore >= $data['limits'][1] && $finalCurrentScore < $data['limits'][0]) {
                    $currentState = $state;
                    break;
                }
            }
        }

        $this->manager->upsertSystemStatus(
            $componentName,
            $currentState,
            $finalCurrentScore,
            $systemStatusProvider->getFineScore()
        );
    }
}
