<?php

namespace App\Service;

use App\Entity\BetSlip;
use App\Enum\BetSlipStatus;
use App\Service\Exception\BetSlipValidationException;
use App\Service\Input\BetSlipSettlementInput;

final readonly class BetSlipSettlementService
{
    public function __construct(
        private BetSlipCalculator $betSlipCalculator,
    ) {
    }

    public function settle(BetSlip $betSlip, BetSlipSettlementInput $input): void
    {
        try {
            $status = BetSlipStatus::from($input->status);
        } catch (\ValueError) {
            throw new BetSlipValidationException('bet_slip.api.status_invalid');
        }

        if (BetSlipStatus::PENDING === $status) {
            $betSlip
                ->setStatus(BetSlipStatus::PENDING)
                ->setActualPayout(null)
                ->setSettledAt(null);

            return;
        }

        $settledAt = $input->settledAt ?? new \DateTimeImmutable();

        $actualPayout = match ($status) {
            BetSlipStatus::WON => $this->resolveWonPayout($betSlip, $input),
            BetSlipStatus::LOST => '0.00',
            BetSlipStatus::VOID => $input->actualPayout ?? $betSlip->getStake(),
            BetSlipStatus::CASHOUT, BetSlipStatus::PARTIAL_CASHOUT => $this->requireNonNegativePayout($input->actualPayout),
            default => throw new BetSlipValidationException('bet_slip.api.status_invalid'),
        };

        if (BetSlipStatus::WON === $status && $this->compareDecimal($actualPayout, '0.00', 2) <= 0) {
            throw new BetSlipValidationException('bet_slip.validation.actual_payout_positive_for_won');
        }

        $betSlip
            ->setStatus($status)
            ->setActualPayout($actualPayout)
            ->setSettledAt($settledAt);
    }

    private function resolveWonPayout(BetSlip $betSlip, BetSlipSettlementInput $input): string
    {
        if (null !== $input->actualPayout) {
            return $input->actualPayout;
        }

        return $this->betSlipCalculator->calculate($betSlip)['predictedPayout'];
    }

    private function requireNonNegativePayout(?string $actualPayout): string
    {
        if (null === $actualPayout) {
            throw new BetSlipValidationException('bet_slip.validation.actual_payout_required');
        }

        if ($this->compareDecimal($actualPayout, '0.00', 2) < 0) {
            throw new BetSlipValidationException('bet_slip.validation.actual_payout_non_negative');
        }

        return $actualPayout;
    }

    private function compareDecimal(string $left, string $right, int $scale): int
    {
        $normalize = static function (string $value) use ($scale): int {
            $negative = str_starts_with($value, '-');
            $value = ltrim(trim($value), '+-');
            [$integerPart, $fractionPart] = array_pad(explode('.', $value, 2), 2, '');
            $fractionPart = substr(str_pad($fractionPart, $scale, '0'), 0, $scale);
            $result = ((int) $integerPart * (10 ** $scale)) + (int) $fractionPart;

            return $negative ? -$result : $result;
        };

        return $normalize($left) <=> $normalize($right);
    }
}
