<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Wakeapp\Bundle\SystemStatusBundle\Behaviour\SystemStatusProviderInterface;
use Wakeapp\Bundle\SystemStatusBundle\Enum\SystemStateEnum;
use Wakeapp\Bundle\SystemStatusBundle\Event\SystemStatusEvent;
use Wakeapp\Bundle\SystemStatusBundle\Manager\SystemStatusManager;
use RuntimeException;
use Wakeapp\Bundle\DbalBundle\Exception\WriteDbalException;
use Wakeapp\Bundle\SystemStatusBundle\Model\SystemStatusData;
use Wakeapp\Bundle\SystemStatusBundle\Model\SystemStatusPartData;

final class SystemStatusService
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

    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param SystemStatusManager $manager
     * @param SystemStatusPartService $systemStatusPartService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        SystemStatusManager $manager,
        SystemStatusPartService $systemStatusPartService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->manager = $manager;
        $this->systemStatusPartService = $systemStatusPartService;
        $this->eventDispatcher = $eventDispatcher;
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

        $systemStatusData = $this->checkSystemStatus($systemStatusProvider);

        $this->dispatchSystemStatusEvent($systemStatusData);

    }

    public function dispatchSystemStatusEvent(SystemStatusData $systemStatusData)
    {
        $systemStatusEvent = new SystemStatusEvent($systemStatusData);

        $this->eventDispatcher->dispatch($systemStatusEvent);
    }

    /**
     * @param SystemStatusProviderInterface $systemStatusProvider
     *
     * @return SystemStatusData
     *
     * @throws WriteDbalException
     */
    private function checkSystemStatus(SystemStatusProviderInterface $systemStatusProvider): SystemStatusData
    {
        $finalCurrentScore = 0;

        $componentName = $systemStatusProvider->getComponentName();
        $scoreMapping = $systemStatusProvider->getScoreMapping();
        $currentState = $systemStatusProvider->getDefaultState();

        $systemStatusData = new SystemStatusData(
            $componentName
        );

        $haveCritical = false;
        $paramsList = [];

        $systemStatusPartList = $this->systemStatusPartService->getParts($systemStatusProvider);
        foreach ($systemStatusPartList as $systemStatusPart) {
            $currentScore = $systemStatusPart->check();
            $paramsList[] = [
                'component' => $systemStatusPart->getComponentName(),
                'partType' => $systemStatusPart->getPartTypeName(),
                'currentScore' => $currentScore,
                'completeScore' => $systemStatusPart->getCompleteScore(),
            ];

            $systemStatusData->addPart(
                new SystemStatusPartData(
                    $systemStatusPart->getComponentName(),
                    $systemStatusPart->getPartTypeName(),
                    $systemStatusPart->getCompleteScore(),
                    $currentScore
                )
            );

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
                if ($finalCurrentScore >= $data['limits'][1] && $finalCurrentScore <= $data['limits'][0]) {
                    $currentState = $state;
                    break;
                }
            }
        }

        $systemStatusData->setCurrentScore($finalCurrentScore);
        $systemStatusData->setCurrentState($currentState);
        $systemStatusData->setFineScore($systemStatusProvider->getFineScore());

        $this->manager->upsertSystemStatus(
            $componentName,
            $currentState,
            $finalCurrentScore,
            $systemStatusProvider->getFineScore()
        );

        return $systemStatusData;
    }
}
