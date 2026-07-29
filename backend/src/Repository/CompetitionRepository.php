<?php

namespace App\Repository;

use App\Entity\Competition;
use App\Entity\Sport;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Competition>
 */
final class CompetitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Competition::class);
    }

    /**
     * @return list<Competition>
     */
    public function findActiveVisibleForUserAndSport(User $user, Sport $sport): array
    {
        return $this->createQueryBuilder('competition')
            ->andWhere('competition.sport = :sport')
            ->andWhere('competition.isActive = :isActive')
            ->andWhere('(competition.owner IS NULL OR competition.owner = :owner)')
            ->setParameter('sport', $sport)
            ->setParameter('isActive', true)
            ->setParameter('owner', $user)
            ->orderBy('competition.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function userHasCompetitionWithNameForSport(User $user, Sport $sport, string $name): bool
    {
        return null !== $this->createQueryBuilder('competition')
            ->select('competition.id')
            ->andWhere('competition.owner = :owner')
            ->andWhere('competition.sport = :sport')
            ->andWhere('competition.name = :name')
            ->setParameter('owner', $user)
            ->setParameter('sport', $sport)
            ->setParameter('name', trim($name))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
