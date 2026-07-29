<?php

namespace App\Api;

use App\Entity\Competition;

final class CompetitionViewFactory
{
    /**
     * @return array{id:int, sportId:int, name:string, countryCode:?string, isGlobal:bool}
     */
    public function create(Competition $competition): array
    {
        $sport = $competition->getSport();

        return [
            'id' => $competition->getId(),
            'sportId' => $sport?->getId() ?? 0,
            'name' => $competition->getName(),
            'countryCode' => $competition->getCountryCode(),
            'isGlobal' => $competition->isGlobal(),
        ];
    }
}
