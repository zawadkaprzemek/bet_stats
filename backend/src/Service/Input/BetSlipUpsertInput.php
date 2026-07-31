<?php

namespace App\Service\Input;

final readonly class BetSlipUpsertInput
{
    /**
     * @param list<int> $competitionIds
     * @param list<int> $tagIds
     */
    public function __construct(
        public ?string $name,
        public string $description,
        public int $bookmakerId,
        public string $stake,
        public string $totalOdds,
        public string $stakeType,
        public string $timing,
        public ?int $selectionsCount,
        public string $taxPercent,
        public array $competitionIds,
        public array $tagIds,
        public \DateTimeImmutable $placedAt,
    ) {
    }
}
