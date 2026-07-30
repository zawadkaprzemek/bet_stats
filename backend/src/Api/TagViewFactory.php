<?php

namespace App\Api;

use App\Entity\Tag;

final class TagViewFactory
{
    /**
     * @return array{id:int, name:string, slug:string, emoji:?string, color:?string, isSystem:bool}
     */
    public function create(Tag $tag): array
    {
        return [
            'id' => $tag->getId(),
            'name' => $tag->getName(),
            'slug' => $tag->getSlug(),
            'emoji' => $tag->getEmoji(),
            'color' => $tag->getColor(),
            'isSystem' => $tag->isSystem(),
        ];
    }
}
