<?php

namespace App\Controller;

use App\Api\UserViewFactory;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class MeController extends AbstractController
{
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function __invoke(UserViewFactory $userViewFactory): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => 'Authentication required.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->json($userViewFactory->create($user));
    }
}
