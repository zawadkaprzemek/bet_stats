<?php

namespace App\Repository;

use App\Application\Dashboard\DashboardFilter;
use App\Application\Dashboard\DashboardSummaryReadRepositoryInterface;
use App\Entity\BetSlip;
use App\Entity\User;
use App\Enum\BetSlipStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BetSlip>
 */
final class BetSlipRepository extends ServiceEntityRepository implements DashboardSummaryReadRepositoryInterface
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

    /**
     * @return list<int>
     */
    public function findDashboardBetSlipIdsForUser(User $owner, DashboardFilter $filter): array
    {
        $queryBuilder = $this->createDashboardFilteredQueryBuilder($owner, $filter)
            ->select('betSlip.id')
            ->orderBy('betSlip.id', 'ASC');

        /** @var list<array{id:int|string}> $rows */
        $rows = $queryBuilder->getQuery()->getArrayResult();

        return array_map(static fn (array $row): int => (int) $row['id'], $rows);
    }

    /**
     * @param list<int> $betSlipIds
     *
     * @return array{
     *   totalBetSlips:int,
     *   settledBetSlips:int,
     *   pendingBetSlips:int,
     *   totalStakeCents:int,
     *   totalOddsMillis:int
     * }
     */
    public function getDashboardAggregateByIds(array $betSlipIds): array
    {
        $queryBuilder = $this->createQueryBuilder('betSlip')
            ->select('COUNT(betSlip.id) AS totalBetSlips')
            ->addSelect('SUM(CASE WHEN betSlip.status = :pendingStatus THEN 1 ELSE 0 END) AS pendingBetSlips')
            ->addSelect('SUM(CASE WHEN betSlip.status != :pendingStatus THEN 1 ELSE 0 END) AS settledBetSlips')
            ->addSelect('COALESCE(SUM(betSlip.stake), 0) AS totalStake')
            ->addSelect('COALESCE(SUM(betSlip.totalOdds), 0) AS totalOdds')
            ->andWhere('betSlip.id IN (:betSlipIds)')
            ->setParameter('betSlipIds', $betSlipIds)
            ->setParameter('pendingStatus', BetSlipStatus::PENDING->value)
        ;

        /** @var array{totalBetSlips:string|int, pendingBetSlips:string|int, settledBetSlips:string|int, totalStake:string|int|float, totalOdds:string|int|float} $result */
        $result = $queryBuilder->getQuery()->getSingleResult();

        return [
            'totalBetSlips' => (int) $result['totalBetSlips'],
            'settledBetSlips' => (int) $result['settledBetSlips'],
            'pendingBetSlips' => (int) $result['pendingBetSlips'],
            'totalStakeCents' => $this->decimalToScaledInteger((string) $result['totalStake'], 2),
            'totalOddsMillis' => $this->decimalToScaledInteger((string) $result['totalOdds'], 3),
        ];
    }

    /**
     * @param list<int> $betSlipIds
     *
     * @return list<BetSlip>
     */
    public function findSettledForDashboardSummaryByIds(array $betSlipIds): array
    {
        /** @var list<BetSlip> $result */
        $result = $this->createQueryBuilder('betSlip')
            ->andWhere('betSlip.id IN (:betSlipIds)')
            ->andWhere('betSlip.status != :pendingStatus')
            ->setParameter('betSlipIds', $betSlipIds)
            ->setParameter('pendingStatus', BetSlipStatus::PENDING->value)
            ->orderBy('betSlip.settledAt', 'ASC')
            ->addOrderBy('betSlip.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $result;
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

    private function createDashboardFilteredQueryBuilder(User $owner, DashboardFilter $filter): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('betSlip')
            ->leftJoin('betSlip.competitions', 'competition')
            ->leftJoin('competition.sport', 'sport')
            ->leftJoin('betSlip.tags', 'tag')
            ->andWhere('betSlip.owner = :owner')
            ->setParameter('owner', $owner)
            ->distinct();

        if ($filter->dateFrom instanceof \DateTimeImmutable) {
            $queryBuilder
                ->andWhere('betSlip.placedAt >= :dateFrom')
                ->setParameter('dateFrom', $filter->dateFrom);
        }

        if ($filter->dateTo instanceof \DateTimeImmutable) {
            $queryBuilder
                ->andWhere('betSlip.placedAt <= :dateTo')
                ->setParameter('dateTo', $filter->dateTo);
        }

        if (null !== $filter->bookmakerId) {
            $queryBuilder
                ->andWhere('IDENTITY(betSlip.bookmaker) = :bookmakerId')
                ->setParameter('bookmakerId', $filter->bookmakerId);
        }

        if (null !== $filter->sportId) {
            $queryBuilder
                ->andWhere('sport.id = :sportId')
                ->setParameter('sportId', $filter->sportId);
        }

        if (null !== $filter->competitionId) {
            $queryBuilder
                ->andWhere('competition.id = :competitionId')
                ->setParameter('competitionId', $filter->competitionId);
        }

        if (null !== $filter->tagId) {
            $queryBuilder
                ->andWhere('tag.id = :tagId')
                ->setParameter('tagId', $filter->tagId);
        }

        if (null !== $filter->status) {
            $queryBuilder
                ->andWhere('betSlip.status = :status')
                ->setParameter('status', $filter->status->value);
        }

        return $queryBuilder;
    }

    private function decimalToScaledInteger(string $value, int $scale): int
    {
        $normalized = trim($value);
        $negative = str_starts_with($normalized, '-');
        $normalized = ltrim($normalized, '+-');
        [$integerPart, $fractionPart] = array_pad(explode('.', $normalized, 2), 2, '');
        $fractionPart = substr(str_pad($fractionPart, $scale, '0'), 0, $scale);
        $integerValue = ((int) $integerPart * (10 ** $scale)) + (int) $fractionPart;

        return $negative ? -$integerValue : $integerValue;
    }
}
