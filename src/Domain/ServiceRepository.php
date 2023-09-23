<?php

namespace App\Domain;

interface ServiceRepository
{
    /** @return string[] */
    public function getServiceNames(): array;

    public function getServiceDatabaseDsn(string $serviceName): string;
}