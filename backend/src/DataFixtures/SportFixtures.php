<?php

namespace App\DataFixtures;

use App\Entity\Sport;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class SportFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['sport'];
    }

    public function load(ObjectManager $manager): void
    {
        $sports = [
            ['Piłka nożna', 'pilka-nozna', '⚽', 10],
            ['Tenis', 'tenis', '🎾', 20],
            ['Koszykówka', 'koszykowka', '🏀', 30],
            ['Siatkówka', 'siatkowka', '🏐', 40],
            ['Hokej', 'hokej', '🏒', 50],
            ['Piłka ręczna', 'pilka-reczna', '🤾', 60],
            ['MMA', 'mma', '🥋', 70],
            ['Boks', 'boks', '🥊', 80],
            ['Esport', 'esport', '🎮', 90],
        ];

        foreach ($sports as [$name, $slug, $emoji, $sortOrder]) {
            $sport = new Sport();
            $sport
                ->setName($name)
                ->setSlug($slug)
                ->setEmoji($emoji)
                ->setIsActive(true)
                ->setSortOrder($sortOrder);

            $manager->persist($sport);
        }

        $manager->flush();
    }
}
