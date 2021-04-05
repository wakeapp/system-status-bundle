<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Command;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Wakeapp\Bundle\SystemStatusBundle\Factory\DtoFactory;
use Wakeapp\Bundle\SystemStatusBundle\Service\SystemStatusService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Wakeapp\Bundle\DbalBundle\Exception\WriteDbalException;

final class SystemStatusCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var DtoFactory $dtoFactory
     */
    protected DtoFactory $dtoFactory;

    /**
     * @var SystemStatusService $statusService
     */
    protected SystemStatusService $statusService;

    /**
     * @param DtoFactory $dtoFactory
     * @param SystemStatusService $statusService
     */
    public function __construct(
        DtoFactory $dtoFactory,
        SystemStatusService $statusService
    ) {
        parent::__construct();

        $this->dtoFactory = $dtoFactory;
        $this->statusService = $statusService;
    }

    protected function configure(): void
    {
            $this
                ->setName('system:status')
                ->addArgument('providerName', null, 'provider names')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws WriteDbalException
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $dto = DtoFactory::createFromInput($input);

        $this->statusService->processSystemStatus($dto->getProviderName());

        sleep(1);
    }
}
