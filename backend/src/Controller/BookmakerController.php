<?php

namespace App\Controller;

use App\Api\BookmakerViewFactory;
use App\Repository\BookmakerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class BookmakerController extends AbstractController
{
    #[Route('/api/bookmakers', name: 'api_bookmakers', methods: ['GET'])]
    public function __invoke(BookmakerRepository $bookmakerRepository, BookmakerViewFactory $bookmakerViewFactory): JsonResponse
    {
        $bookmakers = array_map(
            $bookmakerViewFactory->create(...),
            $bookmakerRepository->findActiveOrdered()
        );

        return $this->json($bookmakers);
    }
}
