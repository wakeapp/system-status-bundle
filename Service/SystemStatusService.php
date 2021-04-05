<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Service;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Wakeapp\Bundle\SystemStatusBundle\Behaviour\SystemStatusProviderInterface;
use Wakeapp\Bundle\SystemStatusBundle\Enum\SystemStateEnum;
use Wakeapp\Bundle\SystemStatusBundle\Event\SystemStatusEvent;
use Wakeapp\Bundle\SystemStatusBundle\Manager\SystemStatusManager;
use RuntimeException;
use Wakeapp\Bundle\DbalBundle\Exception\WriteDbalException;
use Wakeapp\Bundle\SystemStatusBundle\Model\SystemStatusData;
use Wakeapp\Bundle\SystemStatusBundle\Model\SystemStatusPartData;

final class SystemStatusService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
        $this->log('Adding provider ' . $systemStatusProvider->getComponentName() . ' to pool');
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
        $this->log('Started processing system status of component ' . $componentName);
        $systemStatusProvider = $this->systemStatusProviderPool[$componentName];
        if (!$systemStatusProvider) {
            $this->log('Throwing exception: System Status Provider not found to component' . $componentName);
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

        if ($systemStatusProvider->doDispatch()) {
            $this->log('DoDispatch -> true; Dispatching Event');
            $this->dispatchSystemStatusEvent($systemStatusData);
        }
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
        $this->log('Started checking System Status with provider: '. $systemStatusProvider->getComponentName());
        $finalCurrentScore = 0;

        $componentName = $systemStatusProvider->getComponentName();
        $scoreMapping = $systemStatusProvider->getScoreMapping();
        $currentState = $systemStatusProvider->getDefaultState();

        $systemStatusData = new SystemStatusData(
            $componentName
        );

        $haveCritical = false;
        $paramsList = [];

        $systemStatusPartList = $this->systemStatusPartService->getParts($systemStatusProvider->getComponentName());
        $this->log('Formed System Status Part List');
        
        foreach ($systemStatusPartList as $systemStatusPart) {
            $currentScore = $systemStatusPart->check();

            $this->log(
                'Checking part ' .
                $systemStatusPart->getPartTypeName() .
                '. Current score is: ' .
                $currentScore
            );

            $paramsList[] = [
                'component' => $systemStatusPart->getComponentName(),
                'partType' => $systemStatusPart->getPartTypeName(),
                'currentScore' => $currentScore,
                'completeScore' => $systemStatusPart->getCompleteScore(),
                'isLatest' => true,
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
            $this->log('Calculating finalCurrentScore: ' . $finalCurrentScore);

            if ((int)$currentScore <= 0) {
                $haveCritical = true;
            }
        }

        $this->log('Inserting SystemStatusPartList to database');
        $this->manager->insertSystemStatusPartList($paramsList);

        if ($haveCritical) {
            $this->log('SystemStatus has Critical state');
            $currentState = SystemStateEnum::CRITICAL;
        } else {
            $this->log('SystemStatus is NOT in Critical state');
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

        $this->log(
            'Upserting System Status: componentName - ' . $componentName . '; ' .
            'currentState - ' . $currentState . '; ' .
            'finalCurrentScore - ' . $finalCurrentScore . ';'
        );
        $this->manager->upsertSystemStatus(
            $componentName,
            $currentState,
            $finalCurrentScore,
            $systemStatusProvider->getFineScore()
        );

        return $systemStatusData;
    }

    /**
     * @param string $message
     */
    private function log(string $message): void
    {
        $this->logger->debug('[PID: ' . getmypid() . '] ' . $message);
    }
}
