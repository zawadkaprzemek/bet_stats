<?php

namespace App\Application\Dashboard;

final readonly class DashboardSummaryResult
{
    public function __construct(
        public int $totalBetSlips,
        public int $settledBetSlips,
        public int $pendingBetSlips,
        public string $totalStake,
        public string $totalProfit,
        public string $roi,
        public string $winRate,
        public string $averageStake,
        public string $averageOdds,
        public int $currentWinningStreak,
        public int $currentLosingStreak,
        public int $bestWinningStreak,
        public int $worstLosingStreak,
    ) {
    }

    /**
     * @return array{
     *   totalBetSlips:int,
     *   settledBetSlips:int,
     *   pendingBetSlips:int,
     *   totalStake:string,
     *   totalProfit:string,
     *   roi:string,
     *   winRate:string,
     *   averageStake:string,
     *   averageOdds:string,
     *   currentWinningStreak:int,
     *   currentLosingStreak:int,
     *   bestWinningStreak:int,
     *   worstLosingStreak:int
     * }
     */
    public function toArray(): array
    {
        return [
            'totalBetSlips' => $this->totalBetSlips,
            'settledBetSlips' => $this->settledBetSlips,
            'pendingBetSlips' => $this->pendingBetSlips,
            'totalStake' => $this->totalStake,
            'totalProfit' => $this->totalProfit,
            'roi' => $this->roi,
            'winRate' => $this->winRate,
            'averageStake' => $this->averageStake,
            'averageOdds' => $this->averageOdds,
            'currentWinningStreak' => $this->currentWinningStreak,
            'currentLosingStreak' => $this->currentLosingStreak,
            'bestWinningStreak' => $this->bestWinningStreak,
            'worstLosingStreak' => $this->worstLosingStreak,
        ];
    }
}
