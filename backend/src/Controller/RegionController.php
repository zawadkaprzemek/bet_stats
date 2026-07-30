<?php

namespace App\Controller;

use App\Api\RegionViewFactory;
use App\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class RegionController extends AbstractController
{
    #[Route('/api/regions', name: 'api_regions', methods: ['GET'])]
    public function __invoke(RegionRepository $regionRepository, RegionViewFactory $regionViewFactory): JsonResponse
    {
        $regions = array_map(
            $regionViewFactory->create(...),
            $regionRepository->findActiveOrdered()
        );

        return $this->json($regions);
    }
}
