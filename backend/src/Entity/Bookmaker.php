<?php

namespace App\Entity;

use App\Repository\BookmakerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookmakerRepository::class)]
#[ORM\Table(name: 'app_bookmaker')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['name'], message: 'bookmaker.validation.name_unique')]
#[UniqueEntity(fields: ['slug'], message: 'bookmaker.validation.slug_unique')]
class Bookmaker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120, unique: true)]
    private string $name = '';

    #[ORM\Column(length: 120, unique: true)]
    private string $slug = '';

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $emoji = null;

    #[ORM\Column]
    private int $sortOrder = 0;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private bool $isLegal = true;

    #[Assert\PositiveOrZero(message: 'bookmaker.validation.default_tax_non_negative')]
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $defaultTaxPercent = '0.00';

    #[Assert\Url(message: 'bookmaker.validation.website_invalid')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = trim($name);

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = trim($slug);

        return $this;
    }

    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    public function setEmoji(?string $emoji): self
    {
        $emoji = null === $emoji ? null : trim($emoji);
        $this->emoji = '' === $emoji ? null : $emoji;

        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isLegal(): bool
    {
        return $this->isLegal;
    }

    public function setIsLegal(bool $isLegal): self
    {
        $this->isLegal = $isLegal;

        return $this;
    }

    public function getDefaultTaxPercent(): string
    {
        return $this->defaultTaxPercent;
    }

    public function setDefaultTaxPercent(string|int|float $defaultTaxPercent): self
    {
        $this->defaultTaxPercent = number_format((float) $defaultTaxPercent, 2, '.', '');

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $website = null === $website ? null : trim($website);
        $this->website = '' === $website ? null : $website;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt ?? new \DateTimeImmutable();
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt ?? new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function initializeTimestamps(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt ??= $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
