<?php

namespace App\Infrastructure;

use App\Domain\ServiceRepository;

final class DbalServiceRepository implements ServiceRepository
{
    public function __construct(private readonly array $servicesConfiguration)
    {

    }

    public function getServiceNames(): array
    {
        return array_map(fn (array $serviceConfiguration) => $serviceConfiguration['name'], $this->servicesConfiguration);
    }

    public function getServiceDatabaseDsn(string $serviceName): string
    {
        foreach ($this->servicesConfiguration as $serviceConfiguration) {
            if ($serviceConfiguration['name'] === $serviceName) {
                return $serviceConfiguration['databaseDsn'];
            }
        }

        throw new \InvalidArgumentException(sprintf("Service with name %s was not found", $serviceName));
    }
}