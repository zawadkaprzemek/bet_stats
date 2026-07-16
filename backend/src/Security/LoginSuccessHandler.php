<?php

namespace App\Security;

use App\Api\UserViewFactory;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

final class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private readonly UserViewFactory $userViewFactory,
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?JsonResponse
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'Authentication failed.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse($this->userViewFactory->create($user));
    }
}
