<?php

namespace App\Api;

use App\Entity\Bookmaker;

final class BookmakerViewFactory
{
    /**
     * @return array{id:int, name:string, slug:string, emoji:?string}
     */
    public function create(Bookmaker $bookmaker): array
    {
        return [
            'id' => $bookmaker->getId(),
            'name' => $bookmaker->getName(),
            'slug' => $bookmaker->getSlug(),
            'emoji' => $bookmaker->getEmoji(),
        ];
    }
}
