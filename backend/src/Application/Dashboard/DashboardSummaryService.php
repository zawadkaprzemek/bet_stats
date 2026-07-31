<?php

namespace App\Application\Dashboard;

use App\Entity\BetSlip;
use App\Entity\User;
use App\Enum\BetSlipStakeType;
use App\Enum\BetSlipStatus;
use App\Service\BetSlipCalculator;

final readonly class DashboardSummaryService
{
    public function __construct(
        private DashboardSummaryReadRepositoryInterface $betSlipRepository,
        private BetSlipCalculator $betSlipCalculator,
    ) {
    }

    public function summarizeForUser(User $user, DashboardFilter $filter): DashboardSummaryResult
    {
        $betSlipIds = $this->betSlipRepository->findDashboardBetSlipIdsForUser($user, $filter);

        if ([] === $betSlipIds) {
            return new DashboardSummaryResult(0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.000', 0, 0, 0, 0);
        }

        $aggregate = $this->betSlipRepository->getDashboardAggregateByIds($betSlipIds);
        $settledBetSlips = $this->betSlipRepository->findSettledForDashboardSummaryByIds($betSlipIds);

        $totalProfitCents = 0;
        $cashProfitCents = 0;
        $cashStakeCents = 0;
        $wonCount = 0;
        $streakStatuses = [];

        foreach ($settledBetSlips as $betSlip) {
            $profit = $this->betSlipCalculator->calculateProfit($betSlip);

            if (null !== $profit) {
                $profitCents = $this->decimalToScaledInteger($profit, 2);
                $totalProfitCents += $profitCents;

                if (BetSlipStakeType::CASH === $betSlip->getStakeType()) {
                    $cashProfitCents += $profitCents;
                }
            }

            if (BetSlipStakeType::CASH === $betSlip->getStakeType()) {
                $cashStakeCents += $this->decimalToScaledInteger($betSlip->getStake(), 2);
            }

            if (BetSlipStatus::WON === $betSlip->getStatus()) {
                ++$wonCount;
            }

            $streakStatuses[] = $betSlip->getStatus();
        }

        [$currentWinningStreak, $currentLosingStreak, $bestWinningStreak, $worstLosingStreak] = $this->calculateStreaks($streakStatuses);

        $roi = $cashStakeCents > 0
            ? $this->scaledIntegerToDecimal($this->divideAndRoundHalfUp($cashProfitCents * 10000, $cashStakeCents), 2)
            : '0.00';
        $winRate = $aggregate['settledBetSlips'] > 0
            ? $this->scaledIntegerToDecimal($this->divideAndRoundHalfUp($wonCount * 10000, $aggregate['settledBetSlips']), 2)
            : '0.00';

        return new DashboardSummaryResult(
            $aggregate['totalBetSlips'],
            $aggregate['settledBetSlips'],
            $aggregate['pendingBetSlips'],
            $this->scaledIntegerToDecimal($aggregate['totalStakeCents'], 2),
            $this->scaledIntegerToDecimal($totalProfitCents, 2),
            $roi,
            $winRate,
            0 === $aggregate['totalBetSlips']
                ? '0.00'
                : $this->scaledIntegerToDecimal($this->divideAndRoundHalfUp($aggregate['totalStakeCents'], $aggregate['totalBetSlips']), 2),
            0 === $aggregate['totalBetSlips']
                ? '0.00'
                : $this->scaledIntegerToDecimal($this->divideAndRoundHalfUp($aggregate['totalOddsMillis'], $aggregate['totalBetSlips']), 3),
            $currentWinningStreak,
            $currentLosingStreak,
            $bestWinningStreak,
            $worstLosingStreak,
        );
    }

    /**
     * @param list<BetSlipStatus> $statuses
     *
     * @return array{0:int, 1:int, 2:int, 3:int}
     */
    private function calculateStreaks(array $statuses): array
    {
        $bestWinningStreak = 0;
        $worstLosingStreak = 0;
        $runningWins = 0;
        $runningLosses = 0;

        foreach ($statuses as $status) {
            if (BetSlipStatus::WON === $status) {
                ++$runningWins;
                $runningLosses = 0;
                $bestWinningStreak = max($bestWinningStreak, $runningWins);

                continue;
            }

            if (BetSlipStatus::LOST === $status) {
                ++$runningLosses;
                $runningWins = 0;
                $worstLosingStreak = max($worstLosingStreak, $runningLosses);

                continue;
            }

            $runningWins = 0;
            $runningLosses = 0;
        }

        $currentWinningStreak = 0;
        $currentLosingStreak = 0;

        for ($index = count($statuses) - 1; $index >= 0; --$index) {
            $status = $statuses[$index];

            if (BetSlipStatus::WON === $status) {
                if ($currentLosingStreak > 0) {
                    break;
                }

                ++$currentWinningStreak;
                continue;
            }

            if (BetSlipStatus::LOST === $status) {
                if ($currentWinningStreak > 0) {
                    break;
                }

                ++$currentLosingStreak;
                continue;
            }

            if ($currentWinningStreak > 0 || $currentLosingStreak > 0) {
                break;
            }

            $currentWinningStreak = 0;
            $currentLosingStreak = 0;
            break;
        }

        return [$currentWinningStreak, $currentLosingStreak, $bestWinningStreak, $worstLosingStreak];
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
