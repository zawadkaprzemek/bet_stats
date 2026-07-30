<?php

namespace App\Api;

use App\Entity\Region;

final class RegionViewFactory
{
    /**
     * @return array{id:int, name:string, slug:string, emoji:?string}
     */
    public function create(Region $region): array
    {
        return [
            'id' => $region->getId(),
            'name' => $region->getName(),
            'slug' => $region->getSlug(),
            'emoji' => $region->getEmoji(),
        ];
    }
}
