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
            ['Piłka nożna', 'pilka-nozna', 'football', 10],
            ['Tenis', 'tenis', 'tennis', 20],
            ['Koszykówka', 'koszykowka', 'basketball', 30],
            ['Siatkówka', 'siatkowka', 'volleyball', 40],
            ['Hokej', 'hokej', 'hockey-puck', 50],
            ['Piłka ręczna', 'pilka-reczna', 'handball', 60],
            ['MMA', 'mma', 'octagon', 70],
            ['Boks', 'boks', 'boxing-glove', 80],
            ['Esport', 'esport', 'gamepad', 90],
        ];

        foreach ($sports as [$name, $slug, $icon, $sortOrder]) {
            $sport = new Sport();
            $sport
                ->setName($name)
                ->setSlug($slug)
                ->setIcon($icon)
                ->setIsActive(true)
                ->setSortOrder($sortOrder);

            $manager->persist($sport);
        }

        $manager->flush();
    }
}
