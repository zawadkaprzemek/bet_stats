import type { BetSlipStatus } from './bet-slip';

export type DashboardFilters = {
  dateFrom?: string;
  dateTo?: string;
  bookmakerId?: number | '';
  sportId?: number | '';
  competitionId?: number | '';
  tagId?: number | '';
  status?: BetSlipStatus | '';
};

export type DashboardSummary = {
  totalBetSlips: number;
  settledBetSlips: number;
  pendingBetSlips: number;
  totalStake: string;
  totalProfit: string;
  roi: string;
  winRate: string;
  averageStake: string;
  averageOdds: string;
  currentWinningStreak: number;
  currentLosingStreak: number;
  bestWinningStreak: number;
  worstLosingStreak: number;
};

export type DashboardSummaryResponse = {
  summary: DashboardSummary;
};
