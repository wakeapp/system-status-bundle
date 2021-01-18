<?php

declare(strict_types=1);

namespace SystemStatusBundle\Dto;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wakeapp\Component\DtoResolver\Dto\DtoResolverInterface;
use Wakeapp\Component\DtoResolver\Dto\DtoResolverTrait;

class SystemStatusDto implements DtoResolverInterface
{
    use DtoResolverTrait;

    protected $providerName;

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $this->optionsResolver = $resolver;

        $this->optionsResolver->setRequired([
            'providerName'
        ]);

        $this->optionsResolver->setAllowedTypes('providerName', 'string');
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }
}
