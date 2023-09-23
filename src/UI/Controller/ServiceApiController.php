<?php declare(strict_types=1);

namespace App\UI\Controller;

use App\Domain\DeadLetterGatewayRegistry;
use Ecotone\Dbal\Recoverability\DbalDeadLetterBuilder;
use Ecotone\Dbal\Recoverability\DbalDeadLetterModule;
use Ecotone\Dbal\Recoverability\DeadLetterGateway;
use Ecotone\Modelling\DistributedBus;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceApiController
{
    public function __construct(private DistributedBus $distributedBus)
    {
    }

    #[Route("/service/{serviceName}/replayAll", methods: ["POST"])]
    public function replayAll(string $serviceName): Response
    {
        $this->distributedBus->sendMessage(
            $serviceName,
            DbalDeadLetterBuilder::getChannelName(DeadLetterGateway::class, DbalDeadLetterBuilder::REPLAY_ALL_CHANNEL),
            "",
        );

        return new RedirectResponse("/service/" . $serviceName);
    }

    #[Route("/service/{serviceName}/replay/{messageId}", methods: ["POST"])]
    public function replay(string $serviceName, string $messageId): Response
    {
        $this->distributedBus->sendMessage(
            $serviceName,
            DbalDeadLetterBuilder::getChannelName(DeadLetterGateway::class, DbalDeadLetterBuilder::REPLAY_CHANNEL),
            $messageId,
        );

        return new RedirectResponse("/service/" . $serviceName);
    }

    #[Route("/service/{serviceName}/delete/{messageId}", methods: ["POST"])]
    public function delete(string $serviceName, string $messageId): Response
    {
        $this->distributedBus->sendMessage(
            $serviceName,
            DbalDeadLetterBuilder::getChannelName(DeadLetterGateway::class, DbalDeadLetterBuilder::DELETE_CHANNEL),
            $messageId,
        );

        return new RedirectResponse("/service/" . $serviceName);
    }
}