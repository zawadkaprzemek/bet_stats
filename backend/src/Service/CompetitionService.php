<?php

namespace App\Service;

use App\Entity\Competition;
use App\Entity\Region;
use App\Entity\Sport;
use App\Entity\User;
use App\Repository\CompetitionRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CompetitionService
{
    public function __construct(
        private CompetitionRepository $competitionRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function userHasCompetitionWithNameForSport(User $user, Sport $sport, string $name): bool
    {
        return $this->competitionRepository->userHasCompetitionWithNameForSport($user, $sport, $name);
    }

    public function createForUser(User $user, Sport $sport, string $name, ?Region $region): Competition
    {
        $competition = new Competition();
        $competition
            ->setSport($sport)
            ->setOwner($user)
            ->setName($name)
            ->setRegion($region)
            ->setIsActive(true);

        return $competition;
    }

    public function save(Competition $competition): void
    {
        $this->entityManager->persist($competition);
        $this->entityManager->flush();
    }
}
