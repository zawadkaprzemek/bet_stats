<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
final class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @return list<Tag>
     */
    public function findVisibleForUserOrdered(User $owner): array
    {
        return $this->createQueryBuilder('tag')
            ->andWhere('tag.isSystem = :isSystem OR tag.owner = :owner')
            ->setParameter('isSystem', true)
            ->setParameter('owner', $owner)
            ->orderBy('tag.isSystem', 'DESC')
            ->addOrderBy('tag.sortOrder', 'ASC')
            ->addOrderBy('tag.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneOwnedByUser(int $tagId, User $owner): ?Tag
    {
        return $this->createQueryBuilder('tag')
            ->andWhere('tag.id = :tagId')
            ->andWhere('tag.owner = :owner')
            ->setParameter('tagId', $tagId)
            ->setParameter('owner', $owner)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function nameExistsInUserScope(User $owner, string $name, ?int $excludeId = null): bool
    {
        $queryBuilder = $this->createQueryBuilder('tag')
            ->select('tag.id')
            ->andWhere('tag.name = :name')
            ->andWhere('(tag.isSystem = :isSystem OR tag.owner = :owner)')
            ->setParameter('name', trim($name))
            ->setParameter('isSystem', true)
            ->setParameter('owner', $owner)
            ->setMaxResults(1);

        if (null !== $excludeId) {
            $queryBuilder
                ->andWhere('tag.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return null !== $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function slugExistsInUserScope(User $owner, string $slug, ?int $excludeId = null): bool
    {
        $queryBuilder = $this->createQueryBuilder('tag')
            ->select('tag.id')
            ->andWhere('tag.slug = :slug')
            ->andWhere('(tag.isSystem = :isSystem OR tag.owner = :owner)')
            ->setParameter('slug', trim($slug))
            ->setParameter('isSystem', true)
            ->setParameter('owner', $owner)
            ->setMaxResults(1);

        if (null !== $excludeId) {
            $queryBuilder
                ->andWhere('tag.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return null !== $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
