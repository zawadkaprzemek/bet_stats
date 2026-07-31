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

    /**
     * @return list<Competition>
     */
    public function findActiveVisibleForUser(User $user): array
    {
        return $this->createQueryBuilder('competition')
            ->leftJoin('competition.sport', 'sport')
            ->leftJoin('competition.region', 'region')
            ->addSelect('sport', 'region')
            ->andWhere('competition.isActive = :isActive')
            ->andWhere('(competition.owner IS NULL OR competition.owner = :owner)')
            ->setParameter('isActive', true)
            ->setParameter('owner', $user)
            ->orderBy('sport.sortOrder', 'ASC')
            ->addOrderBy('sport.name', 'ASC')
            ->addOrderBy('competition.name', 'ASC')
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

    /**
     * @param list<int> $competitionIds
     * @param list<int> $existingIds
     *
     * @return list<Competition>
     */
    public function findAssignableByIdsForUser(User $user, array $competitionIds, array $existingIds = []): array
    {
        $queryBuilder = $this->createQueryBuilder('competition')
            ->leftJoin('competition.sport', 'sport')
            ->leftJoin('competition.region', 'region')
            ->addSelect('sport', 'region')
            ->andWhere('competition.id IN (:ids)')
            ->andWhere('(competition.owner IS NULL OR competition.owner = :owner)')
            ->setParameter('ids', $competitionIds)
            ->setParameter('owner', $user)
            ->orderBy('competition.name', 'ASC');

        if ([] === $existingIds) {
            $queryBuilder->andWhere('competition.isActive = :isActive')
                ->setParameter('isActive', true);
        } else {
            $queryBuilder
                ->andWhere('(competition.isActive = :isActive OR competition.id IN (:existingIds))')
                ->setParameter('isActive', true)
                ->setParameter('existingIds', $existingIds);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
