<?php

namespace App\Application\Dashboard;

use App\Entity\BetSlip;
use App\Entity\User;

interface DashboardSummaryReadRepositoryInterface
{
    /**
     * @return list<int>
     */
    public function findDashboardBetSlipIdsForUser(User $owner, DashboardFilter $filter): array;

    /**
     * @param list<int> $betSlipIds
     *
     * @return array{
     *   totalBetSlips:int,
     *   settledBetSlips:int,
     *   pendingBetSlips:int,
     *   totalStakeCents:int,
     *   totalOddsMillis:int
     * }
     */
    public function getDashboardAggregateByIds(array $betSlipIds): array;

    /**
     * @param list<int> $betSlipIds
     *
     * @return list<BetSlip>
     */
    public function findSettledForDashboardSummaryByIds(array $betSlipIds): array;
}
