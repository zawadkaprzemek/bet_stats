<?php

namespace App\Controller;

use App\Api\UserViewFactory;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MeController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function __invoke(UserViewFactory $userViewFactory): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => $this->translator->trans('api.auth_required')], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->json($userViewFactory->create($user));
    }
}
