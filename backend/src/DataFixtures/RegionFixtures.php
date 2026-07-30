<?php

namespace App\DataFixtures;

use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class RegionFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['region'];
    }

    public function load(ObjectManager $manager): void
    {
        $regions = [
            ['England', 'england', '🏴', 10],
            ['Spain', 'spain', '🇪🇸', 20],
            ['Germany', 'germany', '🇩🇪', 30],
            ['Italy', 'italy', '🇮🇹', 40],
            ['France', 'france', '🇫🇷', 50],
            ['Europe', 'europe', '🇪🇺', 60],
            ['World', 'world', '🌍', 70],
            ['International', 'international', '🌐', 80],
            ['Scotland', 'scotland', '🏴󠁧󠁢󠁳󠁣󠁴󠁿', 90],
            ['Netherlands', 'netherlands', '🇳🇱', 100],
            ['Portugal', 'portugal', '🇵🇹', 110],
            ['Belgium', 'belgium', '🇧🇪', 120],
            ['Poland', 'poland', '🇵🇱', 130],
            ['Turkey', 'turkey', '🇹🇷', 140],
            ['Croatia', 'croatia', '🇭🇷', 150],
            ['Greece', 'greece', '🇬🇷', 160],
            ['Ukraine', 'ukraine', '🇺🇦', 170],
            ['Wales', 'wales', '🏴󠁧󠁢󠁷󠁬󠁳󠁿', 180],
            ['Northern Ireland', 'northern-ireland', '🇬🇧', 190],
            ['Czech Republic', 'czech-republic', '🇨🇿', 200],
            ['Austria', 'austria', '🇦🇹', 210],
            ['Switzerland', 'switzerland', '🇨🇭', 220],
            ['Denmark', 'denmark', '🇩🇰', 230],
            ['Sweden', 'sweden', '🇸🇪', 240],
            ['Norway', 'norway', '🇳🇴', 250],
            ['Finland', 'finland', '🇫🇮', 260],
            ['Serbia', 'serbia', '🇷🇸', 270],
            ['Romania', 'romania', '🇷🇴', 280],
        ];

        foreach ($regions as [$name, $slug, $emoji, $sortOrder]) {
            $region = new Region();
            $region
                ->setName($name)
                ->setSlug($slug)
                ->setEmoji($emoji)
                ->setSortOrder($sortOrder)
                ->setIsActive(true);

            $manager->persist($region);
        }

        $manager->flush();
    }
}
