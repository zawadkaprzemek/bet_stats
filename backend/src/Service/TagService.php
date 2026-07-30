<?php

namespace App\Service;

use App\Entity\Tag;
use App\Entity\User;
use App\Repository\TagRepository;
use App\Service\Exception\TagConflictException;
use App\Service\Exception\TagNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class TagService
{
    public function __construct(
        private TagRepository $tagRepository,
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger,
    ) {
    }

    /**
     * @return list<Tag>
     */
    public function findVisibleForUser(User $owner): array
    {
        return $this->tagRepository->findVisibleForUserOrdered($owner);
    }

    public function createForUser(User $owner, string $name, ?string $emoji, ?string $color): Tag
    {
        $tag = new Tag();
        $tag
            ->setOwner($owner)
            ->setIsSystem(false)
            ->setSortOrder(0);

        $this->hydrateUserTag($tag, $owner, $name, $emoji, $color);

        return $tag;
    }

    public function updateForUser(User $owner, int $tagId, string $name, ?string $emoji, ?string $color): Tag
    {
        $tag = $this->tagRepository->findOneOwnedByUser($tagId, $owner);

        if (null === $tag) {
            throw new TagNotFoundException('tag.api.not_found');
        }

        $this->hydrateUserTag($tag, $owner, $name, $emoji, $color);

        return $tag;
    }

    public function deleteForUser(User $owner, int $tagId): void
    {
        $tag = $this->tagRepository->findOneOwnedByUser($tagId, $owner);

        if (null === $tag) {
            throw new TagNotFoundException('tag.api.not_found');
        }

        $this->entityManager->remove($tag);
        $this->entityManager->flush();
    }

    public function save(Tag $tag): void
    {
        $this->entityManager->persist($tag);
        $this->entityManager->flush();
    }

    private function hydrateUserTag(Tag $tag, User $owner, string $name, ?string $emoji, ?string $color): void
    {
        $slug = $this->slugger->slug(trim($name))->lower()->toString();

        if ($this->tagRepository->nameExistsInUserScope($owner, $name, $tag->getId())) {
            throw new TagConflictException('tag.validation.name_unique');
        }

        if ($this->tagRepository->slugExistsInUserScope($owner, $slug, $tag->getId())) {
            throw new TagConflictException('tag.validation.slug_unique');
        }

        $tag
            ->setName($name)
            ->setSlug($slug)
            ->setEmoji($emoji)
            ->setColor($color);
    }
}
