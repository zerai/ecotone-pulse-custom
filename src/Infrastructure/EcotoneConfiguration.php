<?php declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\DeadLetterGatewayRegistry;
use Ecotone\Amqp\Configuration\AmqpConfiguration;
use Ecotone\Amqp\Distribution\AmqpDistributedBusConfiguration;
use Ecotone\Dbal\Configuration\CustomDeadLetterGateway;
use Ecotone\Dbal\Configuration\DbalConfiguration;
use Ecotone\Messaging\Attribute\Parameter\ConfigurationVariable;
use Ecotone\Messaging\Attribute\ServiceContext;

class EcotoneConfiguration
{
    #[ServiceContext]
    public function distributedConsumer()
    {
        return [
            AmqpDistributedBusConfiguration::createPublisher(),
            AmqpConfiguration::createWithDefaults()
                ->withTransactionOnAsynchronousEndpoints(false)
                ->withTransactionOnCommandBus(false)
                ->withTransactionOnConsoleCommands(false)
        ];
    }

    #[ServiceContext]
    public function registerDeadLetterConfiguration(): array
    {
        $configurations = [];
        foreach (\json_decode(\getenv('SERVICES'), true, flags: JSON_THROW_ON_ERROR) as $serviceConfiguration) {
            $configurations[] = CustomDeadLetterGateway::createWith(
                DeadLetterGatewayRegistry::gatewayReferenceName($serviceConfiguration['name']),
                DeadLetterGatewayRegistry::connectionReferenceName($serviceConfiguration['name'])
            );
        }

        return $configurations;
    }
}