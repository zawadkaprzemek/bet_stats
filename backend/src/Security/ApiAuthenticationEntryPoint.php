<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final class ApiAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, ?AuthenticationException $authException = null): JsonResponse
    {
        if (str_starts_with($request->getPathInfo(), '/api')) {
            return new JsonResponse([
                'message' => 'Wymagane jest uwierzytelnienie.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'message' => 'Wymagane jest uwierzytelnienie.',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
