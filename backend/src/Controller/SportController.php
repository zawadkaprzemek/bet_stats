<?php

namespace App\Controller;

use App\Api\SportViewFactory;
use App\Repository\SportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SportController extends AbstractController
{
    #[Route('/api/sports', name: 'api_sports', methods: ['GET'])]
    public function __invoke(SportRepository $sportRepository, SportViewFactory $sportViewFactory): JsonResponse
    {
        $sports = array_map(
            $sportViewFactory->create(...),
            $sportRepository->findActiveOrdered()
        );

        return $this->json($sports);
    }
}
