<?php

namespace App\Service\Input;

final readonly class BetSlipSettlementInput
{
    public function __construct(
        public string $status,
        public ?string $actualPayout,
        public ?\DateTimeImmutable $settledAt,
    ) {
    }
}
