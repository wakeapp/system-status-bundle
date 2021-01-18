<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Factory;

use Wakeapp\Bundle\SystemStatusBundle\Dto\SystemStatusDto;
use Wakeapp\Bundle\SystemStatusBundle\Service\SystemStatusService;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;

use function sprintf;

final class DtoFactory
{
    protected static array $providerList;

    /**
     * @required
     *
     * @param SystemStatusService $statusService
     */
    public function dependencyInjection(SystemStatusService $statusService): void
    {
        static::$providerList = $statusService->getProviderList();
    }

    /**
     * @param InputInterface $input
     *
     * @return SystemStatusDto
     *
     * @throws ExceptionInterface
     */
    public static function createFromInput(InputInterface $input): SystemStatusDto
    {
        $providerName = $input->getArgument('providerName');

        if (isset(static::$providerList[$providerName]) === false) {
            throw new RuntimeException(sprintf('undefined providerName: %s', $providerName));
        }

        return new SystemStatusDto(['providerName' => $providerName]);
    }
}
