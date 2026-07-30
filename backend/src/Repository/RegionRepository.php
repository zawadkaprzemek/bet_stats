<?php

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Region>
 */
final class RegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    /**
     * @return list<Region>
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('region')
            ->andWhere('region.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('region.sortOrder', 'ASC')
            ->addOrderBy('region.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
