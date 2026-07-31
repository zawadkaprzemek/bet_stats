<?php

namespace App\Api;

use App\Entity\Competition;
use App\Entity\Region;

final class CompetitionViewFactory
{
    /**
     * @return array{
     *   id:int,
     *   sportId:int,
     *   name:string,
     *   sport:array{id:int, name:string, slug:string, emoji:?string},
     *   region:?array{id:int, name:string, slug:string, emoji:?string},
     *   isGlobal:bool
     * }
     */
    public function create(Competition $competition): array
    {
        $sport = $competition->getSport();
        $region = $competition->getRegion();

        return [
            'id' => $competition->getId(),
            'sportId' => $sport?->getId() ?? 0,
            'name' => $competition->getName(),
            'sport' => [
                'id' => $sport?->getId() ?? 0,
                'name' => $sport?->getName() ?? '',
                'slug' => $sport?->getSlug() ?? '',
                'emoji' => $sport?->getEmoji(),
            ],
            'region' => null === $region ? null : [
                'id' => $region->getId(),
                'name' => $region->getName(),
                'slug' => $region->getSlug(),
                'emoji' => $region->getEmoji(),
            ],
            'isGlobal' => $competition->isGlobal(),
        ];
    }
}
