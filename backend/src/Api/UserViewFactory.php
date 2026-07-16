<?php

namespace App\Api;

use App\Entity\User;

final class UserViewFactory
{
    /**
     * @return array{id:int, email:string, roles:list<string>, createdAt:string, updatedAt:string}
     */
    public function create(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'createdAt' => $user->getCreatedAt()->format(\DATE_ATOM),
            'updatedAt' => $user->getUpdatedAt()->format(\DATE_ATOM),
        ];
    }
}
