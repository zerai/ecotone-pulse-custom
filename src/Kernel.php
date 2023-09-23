<?php

namespace App;

use App\Domain\DeadLetterGatewayRegistry;
use Enqueue\Dbal\DbalConnectionFactory;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    protected function prepareContainer(ContainerBuilder $container)
    {
        parent::prepareContainer($container);
    }

    public function process(ContainerBuilder $container)
    {
        foreach (\json_decode(\getenv('SERVICES'), true, flags: JSON_THROW_ON_ERROR) as $serviceConfiguration) {
            $container->register(
                DeadLetterGatewayRegistry::connectionReferenceName($serviceConfiguration['name']) . '-proxy',
                DbalConnectionFactory::class
            )
                ->setArgument(0, $serviceConfiguration['databaseDsn'])
                ->setPublic(true);
        }
    }
}
