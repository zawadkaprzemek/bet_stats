<?php

namespace App\Repository;

use App\Entity\Bookmaker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bookmaker>
 */
final class BookmakerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bookmaker::class);
    }

    /**
     * @return list<Bookmaker>
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('bookmaker')
            ->andWhere('bookmaker.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('bookmaker.sortOrder', 'ASC')
            ->addOrderBy('bookmaker.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
