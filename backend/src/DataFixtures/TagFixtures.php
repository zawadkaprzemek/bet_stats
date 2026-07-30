<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class TagFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['tag'];
    }

    public function load(ObjectManager $manager): void
    {
        $tags = [
            ['Challenge', 'challenge', '🎯', '#F97316', 10],
            ['Freebet', 'freebet', '🎁', '#06B6D4', 20],
            ['Funbet', 'funbet', '🎉', '#A855F7', 30],
            ['Live', 'live', '⚡', '#EAB308', 40],
            ['Value', 'value', '💎', '#14B8A6', 50],
            ['Safe', 'safe', '🛡', '#22C55E', 60],
            ['High Odds', 'high-odds', '🔥', '#EF4444', 70],
            ['Weekend', 'weekend', '📅', '#3B82F6', 80],
        ];

        foreach ($tags as [$name, $slug, $emoji, $color, $sortOrder]) {
            $tag = new Tag();
            $tag
                ->setName($name)
                ->setSlug($slug)
                ->setEmoji($emoji)
                ->setColor($color)
                ->setIsSystem(true)
                ->setSortOrder($sortOrder)
                ->setOwner(null);

            $manager->persist($tag);
        }

        $manager->flush();
    }
}
