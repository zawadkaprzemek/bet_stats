<?php

namespace App\DataFixtures;

use App\Entity\Bookmaker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class BookmakerFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['bookmaker'];
    }

    public function load(ObjectManager $manager): void
    {
        $bookmakers = [
            ['Fortuna', 'fortuna', '🟢', 10, '12.00', 'https://www.efortuna.pl'],
            ['STS', 'sts', '🔴', 20, '12.00', 'https://www.sts.pl'],
            ['Superbet', 'superbet', '🟠', 30, '12.00', 'https://www.superbet.pl'],
            ['Betclic', 'betclic', '⚫', 40, '12.00', 'https://www.betclic.pl'],
            ['Betfan', 'betfan', '🟣', 50, '12.00', 'https://betfan.pl'],
            ['Forbet', 'forbet', '🔵', 60, '12.00', 'https://www.forbet.pl'],
            ['ETOTO', 'etoto', '🟡', 70, '12.00', 'https://www.etoto.pl'],
            ['LV BET', 'lv-bet', '🟤', 80, '12.00', 'https://lvbet.pl'],
            ['Fuksiarz', 'fuksiarz', '🦊', 90, '12.00', 'https://fuksiarz.pl'],
            ['Totalbet', 'totalbet', '💙', 100, '12.00', 'https://www.totalbet.pl'],
            ['PZBuk', 'pzbuk', '🛡️', 110, '12.00', 'https://www.pzbuk.pl'],
        ];

        foreach ($bookmakers as [$name, $slug, $emoji, $sortOrder, $defaultTaxPercent, $website]) {
            $bookmaker = new Bookmaker();
            $bookmaker
                ->setName($name)
                ->setSlug($slug)
                ->setEmoji($emoji)
                ->setSortOrder($sortOrder)
                ->setIsActive(true)
                ->setIsLegal(true)
                ->setDefaultTaxPercent($defaultTaxPercent)
                ->setWebsite($website);

            $manager->persist($bookmaker);
        }

        $manager->flush();
    }
}
