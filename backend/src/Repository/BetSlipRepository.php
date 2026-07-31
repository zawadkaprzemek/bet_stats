<?php

namespace App\Repository;

use App\Entity\BetSlip;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BetSlip>
 */
final class BetSlipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BetSlip::class);
    }

    public function findOneOwnedByUser(int $id, User $owner): ?BetSlip
    {
        return $this->createBaseOwnedQueryBuilder($owner)
            ->andWhere('betSlip.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function createFilteredOwnedQueryBuilder(User $owner, array $filters): QueryBuilder
    {
        $queryBuilder = $this->createBaseOwnedQueryBuilder($owner);

        if (is_string($filters['status'] ?? null) && '' !== trim($filters['status'])) {
            $queryBuilder
                ->andWhere('betSlip.status = :status')
                ->setParameter('status', trim($filters['status']));
        }

        if (is_numeric($filters['bookmaker'] ?? null)) {
            $queryBuilder
                ->andWhere('bookmaker.id = :bookmakerId')
                ->setParameter('bookmakerId', (int) $filters['bookmaker']);
        }

        if (is_string($filters['stakeType'] ?? null) && '' !== trim($filters['stakeType'])) {
            $queryBuilder
                ->andWhere('betSlip.stakeType = :stakeType')
                ->setParameter('stakeType', trim($filters['stakeType']));
        }

        if (is_string($filters['timing'] ?? null) && '' !== trim($filters['timing'])) {
            $queryBuilder
                ->andWhere('betSlip.timing = :timing')
                ->setParameter('timing', trim($filters['timing']));
        }

        if (is_numeric($filters['competition'] ?? null)) {
            $queryBuilder
                ->innerJoin('betSlip.competitions', 'competitionFilter')
                ->andWhere('competitionFilter.id = :competitionId')
                ->setParameter('competitionId', (int) $filters['competition']);
        }

        if (is_numeric($filters['tag'] ?? null)) {
            $queryBuilder
                ->innerJoin('betSlip.tags', 'tagFilter')
                ->andWhere('tagFilter.id = :tagId')
                ->setParameter('tagId', (int) $filters['tag']);
        }

        if (is_string($filters['placedFrom'] ?? null) && '' !== trim($filters['placedFrom'])) {
            $placedFrom = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, trim($filters['placedFrom']))
                ?: \DateTimeImmutable::createFromFormat('Y-m-d', trim($filters['placedFrom']));

            if ($placedFrom instanceof \DateTimeImmutable) {
                $queryBuilder
                    ->andWhere('betSlip.placedAt >= :placedFrom')
                    ->setParameter('placedFrom', $placedFrom);
            }
        }

        if (is_string($filters['placedTo'] ?? null) && '' !== trim($filters['placedTo'])) {
            $placedTo = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, trim($filters['placedTo']))
                ?: \DateTimeImmutable::createFromFormat('Y-m-d', trim($filters['placedTo']));

            if ($placedTo instanceof \DateTimeImmutable) {
                if (10 === strlen(trim($filters['placedTo']))) {
                    $placedTo = $placedTo->setTime(23, 59, 59);
                }

                $queryBuilder
                    ->andWhere('betSlip.placedAt <= :placedTo')
                    ->setParameter('placedTo', $placedTo);
            }
        }

        if (is_string($filters['search'] ?? null) && '' !== trim($filters['search'])) {
            $queryBuilder
                ->andWhere('(LOWER(COALESCE(betSlip.name, \'\')) LIKE :search OR LOWER(betSlip.description) LIKE :search)')
                ->setParameter('search', '%'.mb_strtolower(trim($filters['search'])).'%');
        }

        return $queryBuilder
            ->orderBy('betSlip.placedAt', 'DESC')
            ->addOrderBy('betSlip.id', 'DESC');
    }

    private function createBaseOwnedQueryBuilder(User $owner): QueryBuilder
    {
        return $this->createQueryBuilder('betSlip')
            ->addSelect('bookmaker', 'competitions', 'tags', 'sport', 'region')
            ->innerJoin('betSlip.bookmaker', 'bookmaker')
            ->leftJoin('betSlip.competitions', 'competitions')
            ->leftJoin('competitions.sport', 'sport')
            ->leftJoin('competitions.region', 'region')
            ->leftJoin('betSlip.tags', 'tags')
            ->andWhere('betSlip.owner = :owner')
            ->setParameter('owner', $owner)
            ->distinct();
    }
}
