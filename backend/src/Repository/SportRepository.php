<?php

namespace App\Repository;

use App\Entity\Sport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sport>
 */
final class SportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sport::class);
    }

    /**
     * @return list<Sport>
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('sport')
            ->andWhere('sport.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('sport.sortOrder', 'ASC')
            ->addOrderBy('sport.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
