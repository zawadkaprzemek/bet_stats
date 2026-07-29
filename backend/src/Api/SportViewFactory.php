<?php

namespace App\Api;

use App\Entity\Sport;

final class SportViewFactory
{
    /**
     * @return array{id:int, name:string, slug:string, icon:string}
     */
    public function create(Sport $sport): array
    {
        return [
            'id' => $sport->getId(),
            'name' => $sport->getName(),
            'slug' => $sport->getSlug(),
            'icon' => $sport->getIcon(),
        ];
    }
}
