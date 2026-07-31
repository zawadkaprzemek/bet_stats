<?php

namespace App\Api;

use App\Entity\BetSlip;
use App\Entity\Competition;
use App\Entity\Tag;
use App\Service\BetSlipCalculator;

final readonly class BetSlipViewFactory
{
    public function __construct(
        private BetSlipCalculator $betSlipCalculator,
    ) {
    }

    /**
     * @return array{
     *   id:int|null,
     *   name:?string,
     *   description:string,
     *   bookmaker:array{id:int|null, name:string, slug:string, emoji:?string, defaultTaxPercent:string},
     *   stake:string,
     *   totalOdds:string,
     *   stakeType:string,
     *   timing:string,
     *   status:string,
     *   selectionsCount:?int,
     *   taxPercent:string,
     *   predictedPayout:string,
     *   actualPayout:?string,
     *   profit:?string,
     *   roi:?string,
     *   placedAt:string,
     *   settledAt:?string,
     *   competitions:list<array{id:int|null, name:string, sport:array{id:int|null, name:string, slug:string, emoji:?string}, region:?array{id:int|null, name:string, slug:string, emoji:?string}, isGlobal:bool}>,
     *   tags:list<array{id:int|null, name:string, slug:string, emoji:?string, color:?string, isSystem:bool}>
     * }
     */
    public function create(BetSlip $betSlip): array
    {
        $bookmaker = $betSlip->getBookmaker();
        $metrics = $this->betSlipCalculator->calculate($betSlip);

        return [
            'id' => $betSlip->getId(),
            'name' => $betSlip->getName(),
            'description' => $betSlip->getDescription(),
            'bookmaker' => [
                'id' => $bookmaker?->getId(),
                'name' => $bookmaker?->getName() ?? '',
                'slug' => $bookmaker?->getSlug() ?? '',
                'emoji' => $bookmaker?->getEmoji(),
                'defaultTaxPercent' => $bookmaker?->getDefaultTaxPercent() ?? '0.00',
            ],
            'stake' => $betSlip->getStake(),
            'totalOdds' => $betSlip->getTotalOdds(),
            'stakeType' => $betSlip->getStakeType()->value,
            'timing' => $betSlip->getTiming()->value,
            'status' => $betSlip->getStatus()->value,
            'selectionsCount' => $betSlip->getSelectionsCount(),
            'taxPercent' => $betSlip->getTaxPercent(),
            'predictedPayout' => $metrics['predictedPayout'],
            'actualPayout' => $betSlip->getActualPayout(),
            'profit' => $metrics['profit'],
            'roi' => $metrics['roi'],
            'placedAt' => $betSlip->getPlacedAt()->format(\DateTimeInterface::ATOM),
            'settledAt' => $betSlip->getSettledAt()?->format(\DateTimeInterface::ATOM),
            'competitions' => array_map(
                static fn (Competition $competition): array => [
                    'id' => $competition->getId(),
                    'name' => $competition->getName(),
                    'sport' => [
                        'id' => $competition->getSport()?->getId(),
                        'name' => $competition->getSport()?->getName() ?? '',
                        'slug' => $competition->getSport()?->getSlug() ?? '',
                        'emoji' => $competition->getSport()?->getEmoji(),
                    ],
                    'region' => null === $competition->getRegion() ? null : [
                        'id' => $competition->getRegion()?->getId(),
                        'name' => $competition->getRegion()?->getName() ?? '',
                        'slug' => $competition->getRegion()?->getSlug() ?? '',
                        'emoji' => $competition->getRegion()?->getEmoji(),
                    ],
                    'isGlobal' => $competition->isGlobal(),
                ],
                $betSlip->getCompetitions()->toArray(),
            ),
            'tags' => array_map(
                static fn (Tag $tag): array => [
                    'id' => $tag->getId(),
                    'name' => $tag->getName(),
                    'slug' => $tag->getSlug(),
                    'emoji' => $tag->getEmoji(),
                    'color' => $tag->getColor(),
                    'isSystem' => $tag->isSystem(),
                ],
                $betSlip->getTags()->toArray(),
            ),
        ];
    }
}
