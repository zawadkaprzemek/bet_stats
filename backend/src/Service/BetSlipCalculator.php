<?php

namespace App\Service;

use App\Entity\BetSlip;
use App\Enum\BetSlipStakeType;
use App\Enum\BetSlipStatus;

final class BetSlipCalculator
{
    /**
     * @return array{predictedPayout:string, profit:?string, roi:?string}
     */
    public function calculate(BetSlip $betSlip): array
    {
        return [
            'predictedPayout' => $this->calculatePredictedPayout(
                $betSlip->getStake(),
                $betSlip->getTotalOdds(),
                $betSlip->getTaxPercent(),
                $betSlip->getStakeType(),
            ),
            'profit' => $this->calculateProfit($betSlip),
            'roi' => $this->calculateRoi($betSlip),
        ];
    }

    public function calculatePredictedPayout(
        string $stake,
        string $totalOdds,
        string $taxPercent,
        BetSlipStakeType $stakeType,
    ): string {
        $stakeCents = $this->decimalToScaledInteger($stake, 2);
        $oddsMillis = $this->decimalToScaledInteger($totalOdds, 3);
        $taxBasisPoints = $this->decimalToScaledInteger($taxPercent, 2);
        $taxedStakeNumerator = $stakeCents * (10000 - $taxBasisPoints);

        if (BetSlipStakeType::CASH === $stakeType) {
            $payoutCents = $this->divideAndRoundHalfUp($taxedStakeNumerator * $oddsMillis, 10000 * 1000);

            return $this->scaledIntegerToDecimal($payoutCents, 2);
        }

        $netOddsMillis = max(0, $oddsMillis - 1000);
        $payoutCents = $this->divideAndRoundHalfUp($taxedStakeNumerator * $netOddsMillis, 10000 * 1000);

        return $this->scaledIntegerToDecimal($payoutCents, 2);
    }

    public function calculateProfit(BetSlip $betSlip): ?string
    {
        if (BetSlipStatus::PENDING === $betSlip->getStatus() || null === $betSlip->getActualPayout()) {
            return null;
        }

        $actualPayoutCents = $this->decimalToScaledInteger($betSlip->getActualPayout(), 2);

        if (BetSlipStakeType::CASH === $betSlip->getStakeType()) {
            $stakeCents = $this->decimalToScaledInteger($betSlip->getStake(), 2);

            return $this->scaledIntegerToDecimal($actualPayoutCents - $stakeCents, 2);
        }

        return $this->scaledIntegerToDecimal($actualPayoutCents, 2);
    }

    public function calculateRoi(BetSlip $betSlip): ?string
    {
        if (BetSlipStakeType::CASH !== $betSlip->getStakeType()) {
            return null;
        }

        $profit = $this->calculateProfit($betSlip);

        if (null === $profit) {
            return null;
        }

        $profitCents = $this->decimalToScaledInteger($profit, 2);
        $stakeCents = $this->decimalToScaledInteger($betSlip->getStake(), 2);

        if ($stakeCents <= 0) {
            return null;
        }

        $roiBasisPoints = $this->divideAndRoundHalfUp($profitCents * 10000, $stakeCents);

        return $this->scaledIntegerToDecimal($roiBasisPoints, 2);
    }

    private function decimalToScaledInteger(string $value, int $scale): int
    {
        $normalized = trim($value);
        $negative = str_starts_with($normalized, '-');
        $normalized = ltrim($normalized, '+-');
        [$integerPart, $fractionPart] = array_pad(explode('.', $normalized, 2), 2, '');
        $fractionPart = substr(str_pad($fractionPart, $scale, '0'), 0, $scale);
        $integerValue = ((int) $integerPart * (10 ** $scale)) + (int) $fractionPart;

        return $negative ? -$integerValue : $integerValue;
    }

    private function scaledIntegerToDecimal(int $value, int $scale): string
    {
        $negative = $value < 0;
        $value = abs($value);
        $divisor = 10 ** $scale;
        $integerPart = intdiv($value, $divisor);
        $fractionPart = str_pad((string) ($value % $divisor), $scale, '0', STR_PAD_LEFT);

        return sprintf('%s%d.%s', $negative ? '-' : '', $integerPart, $fractionPart);
    }

    private function divideAndRoundHalfUp(int $numerator, int $denominator): int
    {
        $adjustment = intdiv($denominator, 2);

        if ($numerator < 0) {
            return intdiv($numerator - $adjustment, $denominator);
        }

        return intdiv($numerator + $adjustment, $denominator);
    }
}
