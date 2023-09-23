<?php declare(strict_types=1);

namespace App\UI\Controller;

use App\Domain\DeadLetterGatewayRegistry;
use App\Domain\ServiceRepository;
use Ecotone\Dbal\Recoverability\DeadLetterGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceViewController extends AbstractController
{
    private const PAGE_SIZE_LIMIT = 20;

    public function __construct(private readonly ServiceRepository $serviceRepository, private readonly DeadLetterGatewayRegistry $deadLetterGatewayRegistry)
    {
    }

    #[Route("/")]
    public function serviceList(): Response
    {
        return $this->render("main.html.twig", [
            "serviceNames" => $this->serviceRepository->getServiceNames(),
            "services" => array_map(function (string $serviceName) {
                return [
                    "name" => $serviceName,
                    "errorsCount" => $this->getLetterGateway($serviceName)->count()
                ];
            }, $this->serviceRepository->getServiceNames())
        ]);
    }

    #[Route("/service/{serviceName}")]
    public function serviceDetails(Request $request, string $serviceName): Response
    {
        $page = $request->query->get('page', 1);

        return $this->render("details.html.twig", [
            "serviceNames" => $this->serviceRepository->getServiceNames(),
            "serviceName" => $serviceName,
            "pagesCount" => ceil($this->getLetterGateway($serviceName)->count() / self::PAGE_SIZE_LIMIT),
            'errors' => $this->getLetterGateway($serviceName)->list(self::PAGE_SIZE_LIMIT, ($page - 1) * self::PAGE_SIZE_LIMIT)
        ]);
    }

    private function getLetterGateway(string $serviceName): DeadLetterGateway
    {
        return $this->deadLetterGatewayRegistry->getFor($serviceName);
    }
}