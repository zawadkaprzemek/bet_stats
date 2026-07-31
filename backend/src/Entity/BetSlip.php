<?php

namespace App\Entity;

use App\Enum\BetSlipStakeType;
use App\Enum\BetSlipStatus;
use App\Enum\BetSlipTiming;
use App\Repository\BetSlipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BetSlipRepository::class)]
#[ORM\Table(name: 'app_bet_slip')]
#[ORM\Index(name: 'idx_bet_slip_owner_placed_at', columns: ['owner_id', 'placed_at'])]
#[ORM\Index(name: 'idx_bet_slip_owner_status', columns: ['owner_id', 'status'])]
#[ORM\Index(name: 'idx_bet_slip_owner_bookmaker', columns: ['owner_id', 'bookmaker_id'])]
#[ORM\HasLifecycleCallbacks]
class BetSlip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $owner = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Bookmaker $bookmaker = null;

    #[Assert\Length(max: 120, maxMessage: 'bet_slip.validation.name_too_long')]
    #[ORM\Column(length: 120, nullable: true)]
    private ?string $name = null;

    #[Assert\NotBlank(message: 'bet_slip.validation.description_required')]
    #[ORM\Column(type: Types::TEXT)]
    private string $description = '';

    #[Assert\Regex(pattern: '/^\d{1,10}\.\d{2}$/', message: 'bet_slip.validation.stake_invalid')]
    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private string $stake = '0.00';

    #[Assert\Regex(pattern: '/^\d{1,7}\.\d{3}$/', message: 'bet_slip.validation.total_odds_invalid')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3)]
    private string $totalOdds = '1.000';

    #[ORM\Column(enumType: BetSlipStakeType::class, length: 32)]
    private BetSlipStakeType $stakeType = BetSlipStakeType::CASH;

    #[ORM\Column(enumType: BetSlipTiming::class, length: 32)]
    private BetSlipTiming $timing = BetSlipTiming::PRE_MATCH;

    #[ORM\Column(enumType: BetSlipStatus::class, length: 32)]
    private BetSlipStatus $status = BetSlipStatus::PENDING;

    #[Assert\Positive(message: 'bet_slip.validation.selections_count_positive')]
    #[ORM\Column(nullable: true)]
    private ?int $selectionsCount = null;

    #[Assert\Regex(pattern: '/^\d{1,3}\.\d{2}$/', message: 'bet_slip.validation.tax_percent_invalid')]
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $taxPercent = '0.00';

    #[Assert\Regex(pattern: '/^\d{1,10}\.\d{2}$/', message: 'bet_slip.validation.actual_payout_invalid')]
    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $actualPayout = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $placedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $settledAt = null;

    /**
     * @var Collection<int, Competition>
     */
    #[ORM\ManyToMany(targetEntity: Competition::class)]
    #[ORM\JoinTable(name: 'app_bet_slip_competition')]
    private Collection $competitions;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: 'app_bet_slip_tag')]
    private Collection $tags;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->competitions = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getBookmaker(): ?Bookmaker
    {
        return $this->bookmaker;
    }

    public function setBookmaker(?Bookmaker $bookmaker): self
    {
        $this->bookmaker = $bookmaker;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $name = null === $name ? null : trim($name);
        $this->name = '' === $name ? null : $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $description = preg_replace('/\s+/u', ' ', trim($description)) ?? '';
        $this->description = $description;

        return $this;
    }

    public function getStake(): string
    {
        return $this->stake;
    }

    public function setStake(string|int|float $stake): self
    {
        $this->stake = number_format((float) $stake, 2, '.', '');

        return $this;
    }

    public function getTotalOdds(): string
    {
        return $this->totalOdds;
    }

    public function setTotalOdds(string|int|float $totalOdds): self
    {
        $this->totalOdds = number_format((float) $totalOdds, 3, '.', '');

        return $this;
    }

    public function getStakeType(): BetSlipStakeType
    {
        return $this->stakeType;
    }

    public function setStakeType(BetSlipStakeType $stakeType): self
    {
        $this->stakeType = $stakeType;

        return $this;
    }

    public function getTiming(): BetSlipTiming
    {
        return $this->timing;
    }

    public function setTiming(BetSlipTiming $timing): self
    {
        $this->timing = $timing;

        return $this;
    }

    public function getStatus(): BetSlipStatus
    {
        return $this->status;
    }

    public function setStatus(BetSlipStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSelectionsCount(): ?int
    {
        return $this->selectionsCount;
    }

    public function setSelectionsCount(?int $selectionsCount): self
    {
        $this->selectionsCount = $selectionsCount;

        return $this;
    }

    public function getTaxPercent(): string
    {
        return $this->taxPercent;
    }

    public function setTaxPercent(string|int|float $taxPercent): self
    {
        $this->taxPercent = number_format((float) $taxPercent, 2, '.', '');

        return $this;
    }

    public function getActualPayout(): ?string
    {
        return $this->actualPayout;
    }

    public function setActualPayout(string|int|float|null $actualPayout): self
    {
        $this->actualPayout = null === $actualPayout ? null : number_format((float) $actualPayout, 2, '.', '');

        return $this;
    }

    public function getPlacedAt(): \DateTimeImmutable
    {
        return $this->placedAt ?? new \DateTimeImmutable();
    }

    public function setPlacedAt(\DateTimeImmutable $placedAt): self
    {
        $this->placedAt = $placedAt;

        return $this;
    }

    public function getSettledAt(): ?\DateTimeImmutable
    {
        return $this->settledAt;
    }

    public function setSettledAt(?\DateTimeImmutable $settledAt): self
    {
        $this->settledAt = $settledAt;

        return $this;
    }

    /**
     * @return Collection<int, Competition>
     */
    public function getCompetitions(): Collection
    {
        return $this->competitions;
    }

    public function addCompetition(Competition $competition): self
    {
        if (!$this->competitions->contains($competition)) {
            $this->competitions->add($competition);
        }

        return $this;
    }

    public function removeCompetition(Competition $competition): self
    {
        $this->competitions->removeElement($competition);

        return $this;
    }

    public function clearCompetitions(): self
    {
        $this->competitions->clear();

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function clearTags(): self
    {
        $this->tags->clear();

        return $this;
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
