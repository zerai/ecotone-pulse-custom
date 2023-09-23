<?php

namespace App\Domain;

use Ecotone\Dbal\Recoverability\DeadLetterGateway;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class DeadLetterGatewayRegistry
{
    public function __construct(private readonly ContainerInterface $container) {}

    public static function gatewayReferenceName(string $serviceName): string
    {
        return "dead_letter_gateway_" . $serviceName;
    }

    public static function connectionReferenceName(string $serviceName): string
    {
        return "dead_letter_connection_" . $serviceName;
    }

    public function getFor(string $serviceName): DeadLetterGateway
    {
        return $this->container->get(self::gatewayReferenceName($serviceName));
    }
}